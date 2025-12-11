<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Services\AssetDeletionService;
use App\Services\AssetMutationService;
use App\Models\AssetDeletion;
use App\Models\AssetMutation;
use App\Models\Maintenance;
use App\Models\Asset;
use App\Models\OpdUnit;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OPDTransactionController extends Controller
{
    private AssetDeletionService $deletionService;
    private AssetMutationService $mutationService;

    public function __construct(
        AssetDeletionService $deletionService,
        AssetMutationService $mutationService
    ) {
        $this->middleware(['auth', 'admin.opd']);
        $this->deletionService = $deletionService;
        $this->mutationService = $mutationService;
    }

    /**
     * Transaction management dengan tab system
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'deletions');
        $status = $request->get('status');
        $opdUnitId = auth()->user()->opd_unit_id;
        
        $data = [];
        
        switch ($tab) {
            case 'deletions':
                $query = AssetDeletion::whereHas('asset', function ($q) use ($opdUnitId) {
                    $q->where('opd_unit_id', $opdUnitId);
                })->with(['asset', 'proposer', 'verifier', 'approver']);
                
                if ($status) {
                    $query->where('status', $status);
                }
                
                $data['deletions'] = $query->orderBy('proposed_at', 'desc')->paginate(20);
                break;
                
            case 'mutations':
                $query = AssetMutation::whereHas('asset', function ($q) use ($opdUnitId) {
                    $q->where('opd_unit_id', $opdUnitId);
                })->with(['asset', 'toOpdUnit', 'fromLocation', 'toLocation']);
                
                if ($status) {
                    $query->where('status', $status);
                }
                
                $data['mutations'] = $query->orderBy('mutation_date', 'desc')->paginate(20);
                
                // Incoming mutations
                $data['incomingMutations'] = AssetMutation::where('to_opd_unit_id', $opdUnitId)
                    ->whereIn('status', ['diusulkan', 'disetujui'])
                    ->with(['asset', 'fromOpdUnit'])
                    ->orderBy('mutation_date', 'desc')
                    ->paginate(10, ['*'], 'incoming_page');
                break;
                
            case 'maintenances':
                $query = Maintenance::whereHas('asset', function ($q) use ($opdUnitId) {
                    $q->where('opd_unit_id', $opdUnitId);
                })->with('asset');
                
                if ($status) {
                    $query->where('status', $status);
                }
                
                $data['maintenances'] = $query->orderBy('scheduled_date', 'desc')->paginate(20);
                
                // Overdue maintenances
                $data['overdueMaintenances'] = Maintenance::whereHas('asset', function ($q) use ($opdUnitId) {
                    $q->where('opd_unit_id', $opdUnitId);
                })
                ->with('asset')
                ->where('status', 'dijadwalkan')
                ->where('scheduled_date', '<', now()->toDateString())
                ->orderBy('scheduled_date')
                ->paginate(10, ['*'], 'overdue_page');
                break;
        }
        
        return view('opd.transactions.index', [
            'title' => ucfirst($tab) . ' Management',
            'tab' => $tab,
            'status' => $status,
            'data' => $data,
        ]);
    }

    /**
     * Form untuk membuat transaksi baru
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'deletion');
        $opdUnitId = auth()->user()->opd_unit_id;
        
        $data = [
            'assets' => Asset::where('opd_unit_id', $opdUnitId)
                ->where('status', 'aktif')
                ->where('document_verification_status', 'valid')
                ->where('validation_status', 'disetujui')
                ->orderBy('name')
                ->get(),
        ];
        
        if ($type === 'mutation') {
            $data['opdUnits'] = OpdUnit::where('opd_unit_id', '!=', $opdUnitId)
                ->orderBy('nama_opd')
                ->get();
            $data['locations'] = Location::where('opd_unit_id', $opdUnitId)
                ->orderBy('name')
                ->get();
        }
        
        if ($type === 'maintenance') {
            $data['maintenanceTypes'] = [
                'rutin' => 'Pemeliharaan Rutin',
                'perbaikan' => 'Perbaikan',
                'kalibrasi' => 'Kalibrasi',
                'penggantian' => 'Penggantian',
                'lainnya' => 'Lainnya',
            ];
        }
        
        if ($request->has('asset_id')) {
            $data['selectedAsset'] = Asset::find($request->asset_id);
        }
        
        return view('opd.transactions.form', [
            'title' => 'Ajukan ' . ucfirst($type) . ' Aset',
            'type' => $type,
            'data' => $data,
            'currentOpdUnit' => auth()->user()->opdUnit,
        ]);
    }

    /**
     * Store deletion proposal
     */
    public function storeDeletion(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,asset_id',
            'deletion_reason' => 'required|in:rusak_berat,hilang,jual,hibah,musnah,lainnya',
            'reason_details' => 'required|string|min:20|max:1000',
            'notes' => 'nullable|string|max:500',
        ]);
        
        try {
            $asset = Asset::findOrFail($request->asset_id);
            
            if ($asset->opd_unit_id !== auth()->user()->opd_unit_id) {
                abort(403, 'Aset tidak ditemukan di OPD Anda');
            }
            
            $data = $request->all();
            $data['proposed_by'] = auth()->id();
            
            $deletion = $this->deletionService->createProposal($data, auth()->id());
            
            return redirect()
                ->route('opd.transactions.show', ['deletion', $deletion->deletion_id])
                ->with('success', 'Proposal penghapusan berhasil diajukan.');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengajukan penghapusan: ' . $e->getMessage());
        }
    }

    /**
     * Store mutation proposal
     */
    public function storeMutation(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,asset_id',
            'to_opd_unit_id' => 'required|exists:opd_units,opd_unit_id',
            'to_location_id' => 'nullable|exists:locations,location_id',
            'mutation_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ]);
        
        try {
            $asset = Asset::findOrFail($request->asset_id);
            
            if ($asset->opd_unit_id !== auth()->user()->opd_unit_id) {
                abort(403, 'Aset tidak ditemukan di OPD Anda');
            }
            
            if ($request->to_opd_unit_id === auth()->user()->opd_unit_id) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'OPD tujuan harus berbeda dari OPD saat ini');
            }
            
            $data = $request->all();
            $data['from_opd_unit_id'] = auth()->user()->opd_unit_id;
            $data['from_location_id'] = $asset->location_id;
            $data['mutated_by'] = auth()->id();
            $data['status'] = 'diusulkan';
            
            $mutation = $this->mutationService->createProposal($data, auth()->id());
            
            return redirect()
                ->route('opd.transactions.show', ['mutation', $mutation->mutation_id])
                ->with('success', 'Proposal mutasi berhasil diajukan.');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengajukan mutasi: ' . $e->getMessage());
        }
    }

    /**
     * Store maintenance record
     */
    public function storeMaintenance(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,asset_id',
            'maintenance_type' => 'required|in:rutin,perbaikan,kalibrasi,penggantian,lainnya',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'cost' => 'nullable|numeric|min:0',
            'vendor' => 'nullable|string|max:255',
            'vendor_contact' => 'nullable|string|max:255',
        ]);
        
        $asset = Asset::findOrFail($request->asset_id);
        if ($asset->opd_unit_id !== auth()->user()->opd_unit_id) {
            abort(403, 'Aset tidak ditemukan di OPD Anda');
        }
        
        try {
            $maintenance = Maintenance::create([
                'asset_id' => $request->asset_id,
                'maintenance_type' => $request->maintenance_type,
                'title' => $request->title,
                'description' => $request->description,
                'scheduled_date' => $request->scheduled_date,
                'status' => 'dijadwalkan',
                'cost' => $request->cost ?? 0,
                'vendor' => $request->vendor,
                'vendor_contact' => $request->vendor_contact,
                'recorded_by' => auth()->id(),
            ]);
            
            if ($request->maintenance_type === 'perbaikan') {
                $asset->update(['status' => 'dalam_perbaikan']);
            }
            
            return redirect()
                ->route('opd.transactions.show', ['maintenance', $maintenance->maintenance_id])
                ->with('success', 'Pemeliharaan berhasil dijadwalkan');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menjadwalkan pemeliharaan: ' . $e->getMessage());
        }
    }

    /**
     * Show transaction detail
     */
    public function show($type, $id)
    {
        $opdUnitId = auth()->user()->opd_unit_id;
        
        switch ($type) {
            case 'deletion':
                $deletion = AssetDeletion::findOrFail($id);
                if ($deletion->asset->opd_unit_id !== $opdUnitId) {
                    abort(403, 'Data tidak ditemukan');
                }
                $deletion->load(['asset.category', 'asset.location', 'proposer', 'verifier', 'approver']);
                $timeline = $this->deletionService->getTimeline($deletion);
                
                return view('opd.transactions.show', [
                    'title' => 'Detail Proposal Penghapusan',
                    'type' => 'deletion',
                    'data' => $deletion,
                    'timeline' => $timeline,
                ]);
                
            case 'mutation':
                $mutation = AssetMutation::findOrFail($id);
                if ($mutation->asset->opd_unit_id !== $opdUnitId &&
                    $mutation->from_opd_unit_id !== $opdUnitId) {
                    abort(403, 'Data tidak ditemukan');
                }
                $mutation->load(['asset.category', 'fromOpdUnit', 'toOpdUnit', 'fromLocation', 'toLocation', 'mutator']);
                
                return view('opd.transactions.show', [
                    'title' => 'Detail Mutasi Aset',
                    'type' => 'mutation',
                    'data' => $mutation,
                    'canAccept' => $mutation->to_opd_unit_id === $opdUnitId && $mutation->status === 'disetujui',
                ]);
                
            case 'maintenance':
                $maintenance = Maintenance::findOrFail($id);
                if ($maintenance->asset->opd_unit_id !== $opdUnitId) {
                    abort(403, 'Data tidak ditemukan');
                }
                $maintenance->load(['asset', 'recorder', 'approver']);
                
                return view('opd.transactions.show', [
                    'title' => 'Detail Pemeliharaan',
                    'type' => 'maintenance',
                    'data' => $maintenance,
                ]);
                
            default:
                abort(404);
        }
    }

    /**
     * Cancel transaction
     */
    public function cancel(Request $request, $type, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|min:10|max:500',
        ]);
        
        try {
            switch ($type) {
                case 'deletion':
                    $deletion = AssetDeletion::findOrFail($id);
                    if ($deletion->asset->opd_unit_id !== auth()->user()->opd_unit_id) {
                        abort(403, 'Hanya OPD pengaju yang dapat membatalkan');
                    }
                    if (!in_array($deletion->status, ['diusulkan', 'diverifikasi'])) {
                        return redirect()->back()->with('error', 'Status tidak dapat dibatalkan');
                    }
                    $this->deletionService->cancelProposal($id, $request->cancellation_reason);
                    break;
                    
                case 'mutation':
                    $mutation = AssetMutation::findOrFail($id);
                    if ($mutation->from_opd_unit_id !== auth()->user()->opd_unit_id) {
                        abort(403, 'Hanya OPD pengaju yang dapat membatalkan');
                    }
                    if ($mutation->status !== 'diusulkan') {
                        return redirect()->back()->with('error', 'Status tidak dapat dibatalkan');
                    }
                    $mutation->update([
                        'status' => 'dibatalkan',
                        'notes' => $mutation->notes . "\n[Dibatalkan] " . $request->cancellation_reason,
                    ]);
                    $mutation->asset->update(['status' => 'aktif']);
                    break;
                    
                case 'maintenance':
                    $maintenance = Maintenance::findOrFail($id);
                    if ($maintenance->asset->opd_unit_id !== auth()->user()->opd_unit_id) {
                        abort(403, 'Data tidak ditemukan');
                    }
                    if ($maintenance->status !== 'dijadwalkan') {
                        return redirect()->back()->with('error', 'Status tidak dapat dibatalkan');
                    }
                    $maintenance->update(['status' => 'dibatalkan']);
                    break;
            }
            
            return redirect()
                ->route('opd.transactions.show', [$type, $id])
                ->with('success', ucfirst($type) . ' berhasil dibatalkan');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal membatalkan: ' . $e->getMessage());
        }
    }

    /**
     * Update maintenance status
     */
    public function updateMaintenanceStatus(Request $request, Maintenance $maintenance)
    {
        if ($maintenance->asset->opd_unit_id !== auth()->user()->opd_unit_id) {
            abort(403, 'Data tidak ditemukan');
        }
        
        $request->validate([
            'status' => 'required|in:dalam_pengerjaan,selesai,ditunda,dibatalkan',
            'actual_date' => 'nullable|date',
            'result_notes' => 'nullable|string',
            'result_status' => 'nullable|in:baik,perlu_perbaikan,rusak',
        ]);
        
        try {
            $data = [
                'status' => $request->status,
                'result_notes' => $request->result_notes,
                'result_status' => $request->result_status,
            ];
            
            if ($request->has('actual_date')) {
                $data['actual_date'] = $request->actual_date;
            }
            
            if ($request->status === 'selesai') {
                $data['approved_by'] = auth()->id();
                $data['approved_at'] = now();
                $maintenance->asset->update(['status' => 'aktif']);
            }
            
            $maintenance->update($data);
            
            return redirect()
                ->route('opd.transactions.show', ['maintenance', $maintenance->maintenance_id])
                ->with('success', 'Status pemeliharaan berhasil diperbarui');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Accept incoming mutation
     */
    public function acceptMutation(Request $request, AssetMutation $mutation)
    {
        if ($mutation->to_opd_unit_id !== auth()->user()->opd_unit_id) {
            return response()->json([
                'success' => false,
                'message' => 'Mutasi ini tidak ditujukan ke OPD Anda',
            ], 403);
        }

        // DIKOREKSI: OPD tujuan menerima proposal yang masih 'diusulkan'
        // bukan yang sudah 'disetujui' oleh admin utama
        if ($mutation->status !== 'diusulkan') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya mutasi yang masih diusulkan yang dapat diterima',
            ], 400);
        }

        try {
            // Get target location if provided, otherwise use default location
            $targetLocation = $mutation->to_location_id
                ? Location::find($mutation->to_location_id)
                : Location::where('opd_unit_id', auth()->user()->opd_unit_id)
                    ->where('type', 'gedung')
                    ->first();

            if (!$targetLocation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan tentukan lokasi tujuan untuk aset ini',
                ], 400);
            }

            // First, update the mutation status to 'disetujui' (approved by OPD tujuan)
            $mutation->update([
                'status' => 'disetujui',
                'to_location_id' => $targetLocation->location_id,
                'notes' => $mutation->notes . "\n[Diterima] Aset diterima oleh OPD tujuan.",
            ]);

            // Then complete the mutation
            $this->mutationService->completeMutation($mutation->mutation_id);

            return response()->json([
                'success' => true,
                'message' => 'Mutasi berhasil diterima. Aset sekarang menjadi milik OPD Anda.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menerima mutasi: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * AJAX: Get transaction statistics
     */
    public function getStatistics(Request $request)
    {
        $type = $request->get('type', 'all');
        $opdUnitId = auth()->user()->opd_unit_id;
        $stats = [];

        if ($type === 'all' || $type === 'deletions') {
            $stats['deletions'] = $this->deletionService->getStatistics($opdUnitId);
        }

        if ($type === 'all' || $type === 'mutations') {
            $stats['mutations'] = $this->mutationService->getStatistics($opdUnitId);
        }

        if ($type === 'all' || $type === 'maintenances') {
            $stats['maintenances'] = [
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
        }

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }
}