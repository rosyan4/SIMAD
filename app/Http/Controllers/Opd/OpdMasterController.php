<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Category;
use App\Models\Asset;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OPDMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin.opd']);
    }

    /**
     * Master data management dengan tab system
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'locations');
        $opdUnitId = auth()->user()->opd_unit_id;
        
        $data = [];
        
        switch ($tab) {
            case 'locations':
                $type = $request->get('type');
                $query = Location::where('opd_unit_id', $opdUnitId)
                    ->withCount('assets')
                    ->orderBy('name');
                
                if ($type && in_array($type, Location::TYPES)) {
                    $query->where('type', $type);
                }
                
                $data['locations'] = $query->paginate(20);
                $data['type'] = $type;
                break;
                
            case 'categories':
                $data['categories'] = Category::orderBy('kib_code')->paginate(20);
                break;
                
            case 'map':
                $data['locations'] = Location::where('opd_unit_id', $opdUnitId)
                    ->withCount('assets')
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->get();
                break;
                
            case 'statistics':
                $data['locationStats'] = $this->getLocationStatistics($opdUnitId);
                $data['categoryStats'] = $this->getCategoryStatistics($opdUnitId);
                break;
        }
        
        return view('opd.master.index', [
            'title' => 'Data Master - ' . ucfirst($tab),
            'tab' => $tab,
            'data' => $data,
            'locationTypes' => Location::TYPES,
        ]);
    }

    /**
     * CRUD untuk locations (AJAX compatible)
     */
    public function locationStore(Request $request)
    {
        $request->validate(Location::rules());
        
        try {
            $location = Location::create([
                'name' => $request->name,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'opd_unit_id' => auth()->user()->opd_unit_id,
                'type' => $request->type,
                'address' => $request->address,
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lokasi berhasil ditambahkan',
                    'location' => $location,
                ]);
            }
            
            return redirect()
                ->route('opd.master.index', ['tab' => 'locations'])
                ->with('success', 'Lokasi berhasil ditambahkan');
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan lokasi: ' . $e->getMessage(),
                ], 400);
            }
            
            return redirect()
                ->route('opd.master.index', ['tab' => 'locations'])
                ->withInput()
                ->with('error', 'Gagal menambahkan lokasi: ' . $e->getMessage());
        }
    }

    public function locationUpdate(Request $request, Location $location)
    {
        if ($location->opd_unit_id !== auth()->user()->opd_unit_id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lokasi tidak ditemukan',
                ], 403);
            }
            abort(403, 'Lokasi tidak ditemukan');
        }
        
        $request->validate(Location::rules($location->location_id));
        
        try {
            $location->update($request->all());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lokasi berhasil diperbarui',
                    'location' => $location->fresh(),
                ]);
            }
            
            return redirect()
                ->route('opd.master.index', ['tab' => 'locations'])
                ->with('success', 'Lokasi berhasil diperbarui');
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui lokasi: ' . $e->getMessage(),
                ], 400);
            }
            
            return redirect()
                ->route('opd.master.index', ['tab' => 'locations'])
                ->withInput()
                ->with('error', 'Gagal memperbarui lokasi: ' . $e->getMessage());
        }
    }

    public function locationDestroy(Location $location)
    {
        if ($location->opd_unit_id !== auth()->user()->opd_unit_id) {
            abort(403, 'Lokasi tidak ditemukan');
        }
        
        if ($location->assets()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Tidak dapat menghapus lokasi yang memiliki aset. Pindahkan aset terlebih dahulu.');
        }
        
        try {
            $location->delete();
            
            return redirect()
                ->route('opd.master.index', ['tab' => 'locations'])
                ->with('success', 'Lokasi berhasil dihapus');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus lokasi: ' . $e->getMessage());
        }
    }

    /**
     * Move asset to different location (AJAX compatible)
     */
    public function moveAsset(Request $request, Asset $asset)
    {
        if ($asset->opd_unit_id !== auth()->user()->opd_unit_id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aset tidak ditemukan',
                ], 403);
            }
            abort(403, 'Aset tidak ditemukan');
        }
        
        $request->validate([
            'location_id' => 'required|exists:locations,location_id',
            'move_reason' => 'required|string|max:500',
        ]);
        
        $location = Location::find($request->location_id);
        if ($location->opd_unit_id !== auth()->user()->opd_unit_id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lokasi tidak ditemukan di OPD Anda',
                ], 400);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Lokasi tidak ditemukan di OPD Anda');
        }
        
        try {
            $oldLocation = $asset->location;
            $asset->update(['location_id' => $request->location_id]);
            
            AssetHistory::create([
                'asset_id' => $asset->asset_id,
                'action' => 'update',
                'description' => "Aset dipindahkan dari lokasi " . ($oldLocation->name ?? 'Tidak ada') .
                    " ke {$location->name}. Alasan: {$request->move_reason}",
                'change_by' => auth()->id(),
                'ip_address' => request()->ip(),
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Aset berhasil dipindahkan ke {$location->name}",
                    'asset' => $asset->fresh(['location']),
                ]);
            }
            
            return redirect()
                ->route('opd.assets.show', $asset)
                ->with('success', "Aset berhasil dipindahkan ke {$location->name}");
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memindahkan aset: ' . $e->getMessage(),
                ], 400);
            }
            
            return redirect()
                ->back()
                ->with('error', 'Gagal memindahkan aset: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Get location details
     */
    public function getLocation(Location $location)
    {
        if ($location->opd_unit_id !== auth()->user()->opd_unit_id) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi tidak ditemukan',
            ], 403);
        }
        
        $location->loadCount('assets');
        $assets = $location->assets()
            ->with('category')
            ->orderBy('name')
            ->limit(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'location' => $location,
            'assets' => $assets,
        ]);
    }

    /**
     * AJAX: Get location statistics
     */
    public function getLocationStats()
    {
        $opdUnitId = auth()->user()->opd_unit_id;
        $stats = $this->getLocationStatistics($opdUnitId);
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get location statistics
     */
    private function getLocationStatistics($opdUnitId)
    {
        return [
            'total_locations' => Location::where('opd_unit_id', $opdUnitId)->count(),
            'locations_with_assets' => Location::where('opd_unit_id', $opdUnitId)
                ->has('assets')
                ->count(),
            'total_assets' => Asset::where('opd_unit_id', $opdUnitId)->count(),
            'type_distribution' => Location::where('opd_unit_id', $opdUnitId)
                ->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->type => $item->count];
                }),
            'top_locations' => Location::where('opd_unit_id', $opdUnitId)
                ->withCount('assets')
                ->orderBy('assets_count', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($location) {
                    return [
                        'id' => $location->location_id,
                        'name' => $location->name,
                        'type' => $location->type,
                        'asset_count' => $location->assets_count,
                    ];
                }),
        ];
    }

    /**
     * Get category statistics
     */
    private function getCategoryStatistics($opdUnitId)
    {
        return DB::table('assets')
            ->join('categories', 'assets.category_id', '=', 'categories.category_id')
            ->select(
                'categories.kib_code',
                'categories.name as category_name',
                DB::raw('COUNT(*) as asset_count'),
                DB::raw('SUM(assets.value) as total_value')
            )
            ->where('assets.opd_unit_id', $opdUnitId)
            ->groupBy('categories.kib_code', 'categories.name')
            ->orderBy('categories.kib_code')
            ->get()
            ->map(function ($item) {
                return [
                    'kib_code' => $item->kib_code,
                    'category_name' => $item->category_name,
                    'asset_count' => $item->asset_count,
                    'total_value' => (float) $item->total_value,
                ];
            });
    }

    /**
     * AJAX: Search locations
     */
    public function searchLocations(Request $request)
    {
        $opdUnitId = auth()->user()->opd_unit_id;
        $search = $request->get('search', '');
        $type = $request->get('type');
        
        $query = Location::where('opd_unit_id', $opdUnitId)
            ->withCount('assets')
            ->orderBy('name');
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }
        
        if ($type && in_array($type, Location::TYPES)) {
            $query->where('type', $type);
        }
        
        $locations = $query->paginate(15);
        
        return response()->json([
            'success' => true,
            'locations' => $locations,
        ]);
    }
}