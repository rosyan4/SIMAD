<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Models\Asset;
use App\Models\Maintenance;
use App\Models\AssetDeletion;
use App\Models\AssetMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OPDDashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware(['auth', 'admin.opd']);
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display OPD dashboard dengan tab-based layout
     */
    public function index(Request $request)
    {
        $opdUnitId = auth()->user()->opd_unit_id;
        $tab = $request->get('tab', 'overview');
        
        // Data umum untuk semua tab
        $dashboardData = $this->dashboardService->getDashboardData($opdUnitId);
        $opdUnit = auth()->user()->opdUnit;
        
        $data = [];
        
        switch ($tab) {
            case 'overview':
                // Data sudah diambil dari dashboard service
                break;
                
            case 'statistics':
                $assets = Asset::where('opd_unit_id', $opdUnitId)->get();
                $data['statistics'] = [
                    'total_assets' => $assets->count(),
                    'total_value' => $assets->sum('value'),
                    'active_assets' => $assets->where('status', 'aktif')->count(),
                    'under_maintenance' => $assets->where('status', 'dalam_perbaikan')->count(),
                    'mutated_assets' => $assets->where('status', 'dimutasi')->count(),
                    'deleted_assets' => $assets->where('status', 'dihapus')->count(),
                ];
                
                $data['conditionDistribution'] = [
                    'Baik' => $assets->where('condition', 'Baik')->count(),
                    'Rusak Ringan' => $assets->where('condition', 'Rusak Ringan')->count(),
                    'Rusak Berat' => $assets->where('condition', 'Rusak Berat')->count(),
                ];
                
                $data['statusDistribution'] = $assets->groupBy('status')->map->count();
                break;
                
            case 'maintenance':
                $data['maintenances'] = Maintenance::whereHas('asset', function ($q) use ($opdUnitId) {
                    $q->where('opd_unit_id', $opdUnitId);
                })
                ->with('asset')
                ->orderBy('scheduled_date')
                ->paginate(10);
                
                $data['upcoming'] = $data['maintenances']->where('scheduled_date', '>=', now()->toDateString())
                    ->where('status', 'dijadwalkan');
                    
                $data['overdue'] = $data['maintenances']->where('scheduled_date', '<', now()->toDateString())
                    ->where('status', 'dijadwalkan');
                break;
                
            case 'activities':
                $data['activities'] = DB::table('asset_histories')
                    ->join('assets', 'asset_histories.asset_id', '=', 'assets.asset_id')
                    ->join('users', 'asset_histories.change_by', '=', 'users.user_id')
                    ->select([
                        'asset_histories.*',
                        'assets.name as asset_name',
                        'assets.asset_code',
                        'users.name as user_name',
                    ])
                    ->where('assets.opd_unit_id', $opdUnitId)
                    ->orderBy('asset_histories.change_date', 'desc')
                    ->paginate(15);
                break;
                
            case 'quick-actions':
                $data['quickStats'] = [
                    'pending_maintenance' => Maintenance::whereHas('asset', function ($q) use ($opdUnitId) {
                        $q->where('opd_unit_id', $opdUnitId);
                    })->where('status', 'dijadwalkan')->count(),
                    
                    'assets_needing_attention' => Asset::where('opd_unit_id', $opdUnitId)
                        ->whereIn('condition', ['Rusak Ringan', 'Rusak Berat'])
                        ->where('status', 'aktif')
                        ->count(),
                    
                    'pending_deletions' => AssetDeletion::whereHas('asset', function ($q) use ($opdUnitId) {
                        $q->where('opd_unit_id', $opdUnitId);
                    })->where('status', 'diusulkan')->count(),
                    
                    'pending_mutations' => AssetMutation::whereHas('asset', function ($q) use ($opdUnitId) {
                        $q->where('opd_unit_id', $opdUnitId);
                    })->where('status', 'diusulkan')->count(),
                    
                    'incoming_mutations' => AssetMutation::where('to_opd_unit_id', $opdUnitId)
                        ->where('status', 'diusulkan')
                        ->count(),
                ];
                break;
        }
        
        return view('opd.dashboard.index', [
            'title' => 'Dashboard OPD - ' . ucfirst($tab),
            'dashboardData' => $dashboardData,
            'opdUnit' => $opdUnit,
            'tab' => $tab,
            'data' => $data,
        ]);
    }

    /**
     * AJAX: Get chart data
     */
    public function chartData()
    {
        $opdUnitId = auth()->user()->opd_unit_id;
        
        // Asset value trend (last 6 months)
        $trendData = DB::table('assets')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as asset_count'),
                DB::raw('SUM(value) as total_value')
            )
            ->where('opd_unit_id', $opdUnitId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => "{$item->year}-" . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'asset_count' => $item->asset_count,
                    'total_value' => (float) $item->total_value,
                ];
            });

        return response()->json([
            'success' => true,
            'trendData' => $trendData,
        ]);
    }

    /**
     * AJAX: Get maintenance statistics
     */
    public function maintenanceStats()
    {
        $opdUnitId = auth()->user()->opd_unit_id;
        
        $stats = [
            'total' => Maintenance::whereHas('asset', function ($q) use ($opdUnitId) {
                $q->where('opd_unit_id', $opdUnitId);
            })->count(),
            
            'scheduled' => Maintenance::whereHas('asset', function ($q) use ($opdUnitId) {
                $q->where('opd_unit_id', $opdUnitId);
            })->where('status', 'dijadwalkan')->count(),
            
            'completed' => Maintenance::whereHas('asset', function ($q) use ($opdUnitId) {
                $q->where('opd_unit_id', $opdUnitId);
            })->where('status', 'selesai')->count(),
            
            'total_cost' => Maintenance::whereHas('asset', function ($q) use ($opdUnitId) {
                $q->where('opd_unit_id', $opdUnitId);
            })->where('status', 'selesai')->sum('cost'),
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }
}