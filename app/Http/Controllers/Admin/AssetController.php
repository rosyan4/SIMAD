<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AssetService;
use App\Models\Asset;
use App\Models\Category;
use App\Models\OpdUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    private AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->middleware(['auth', 'admin.utama']);
        $this->assetService = $assetService;
    }

    /**
     * Display assets with filters
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $type = $request->get('type', 'all'); // all, pending-verification, pending-validation
        $status = $request->get('status');
        
        $filters = $request->only([
            'opd_unit_id', 'category_id', 'condition',
            'document_verification_status', 'validation_status', 'search'
        ]);
        
        // Apply type-specific filters
        if ($type === 'pending-verification') {
            $filters['document_verification_status'] = 'belum_diverifikasi';
        } elseif ($type === 'pending-validation') {
            $filters['validation_status'] = 'belum_divalidasi';
        }
        
        if ($status) {
            $filters['status'] = $status;
        }
        
        $assets = $this->assetService->searchAssets($filters, $perPage);
        
        $opdUnits = OpdUnit::orderBy('kode_opd_numeric')->get();
        $categories = Category::orderBy('kib_code')->get();
        
        $title = match($type) {
            'pending-verification' => 'Aset Menunggu Verifikasi',
            'pending-validation' => 'Aset Menunggu Validasi',
            default => 'Daftar Aset'
        };
        
        return view('admin.assets.index', [
            'title' => $title,
            'assets' => $assets,
            'opdUnits' => $opdUnits,
            'categories' => $categories,
            'filters' => $filters,
            'type' => $type,
            'status' => $status,
        ]);
    }

    /**
     * Display asset detail with tabs
     */
    public function show(Request $request, Asset $asset)
    {
        $tab = $request->get('tab', 'detail');
        $allowedTabs = ['detail', 'history', 'documents', 'audits', 'maintenance', 'mutations'];
        $tab = in_array($tab, $allowedTabs) ? $tab : 'detail';
        
        // Load basic relationships
        $asset->load(['category', 'opdUnit', 'location', 'creator']);
        
        // Load additional relationships based on tab
        switch ($tab) {
            case 'history':
                $asset->load(['histories.changer']);
                break;
            case 'documents':
                $asset->load(['documents.uploader']);
                break;
            case 'audits':
                $asset->load(['audits.auditor']);
                break;
            case 'maintenance':
                $asset->load(['maintenances.recorder', 'maintenances.approver']);
                break;
            case 'mutations':
                $asset->load(['mutations.fromOpdUnit', 'mutations.toOpdUnit', 'mutations.mutator']);
                break;
        }
        
        return view('admin.assets.show', [
            'title' => 'Detail Aset: ' . $asset->asset_code,
            'asset' => $asset,
            'tab' => $tab,
            'allowedTabs' => $allowedTabs,
        ]);
    }

    /**
     * Verify asset document
     */
    public function verifyDocument(Request $request, Asset $asset)
    {
        $request->validate([
            'status' => 'required|in:valid,tidak_valid',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->assetService->verifyDocument(
                $asset,
                $request->status,
                $request->notes
            );
            
            $statusDisplay = $request->status === 'valid' ? 'Valid' : 'Tidak Valid';
            
            return redirect()
                ->route('admin.assets.show', ['asset' => $asset, 'tab' => 'detail'])
                ->with('success', "Dokumen aset berhasil diverifikasi sebagai {$statusDisplay}");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.assets.show', ['asset' => $asset, 'tab' => 'detail'])
                ->with('error', 'Gagal memverifikasi dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Validate asset
     */
    public function validateAsset(Request $request, Asset $asset)
    {
        $request->validate([
            'status' => 'required|in:disetujui,revisi,ditolak',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->assetService->validateAsset(
                $asset,
                $request->status,
                $request->notes
            );
            
            $statusDisplay = match($request->status) {
                'disetujui' => 'Disetujui',
                'revisi' => 'Perlu Revisi',
                'ditolak' => 'Ditolak',
                default => 'Unknown'
            };
            
            return redirect()
                ->route('admin.assets.show', ['asset' => $asset, 'tab' => 'detail'])
                ->with('success', "Aset berhasil divalidasi dengan status: {$statusDisplay}");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.assets.show', ['asset' => $asset, 'tab' => 'detail'])
                ->with('error', 'Gagal memvalidasi aset: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions for assets
     */
    public function bulkActions(Request $request)
    {
        $action = $request->get('action', 'verification'); // verification, validation
        
        $query = Asset::with(['category', 'opdUnit']);
        
        if ($action === 'verification') {
            $query->where('document_verification_status', 'belum_diverifikasi');
            $title = 'Verifikasi Dokumen Massal';
        } else {
            $query->where('validation_status', 'belum_divalidasi');
            $title = 'Validasi Aset Massal';
        }
        
        $assets = $query->orderBy('created_at', 'desc')->get();
        
        return view('admin.assets.bulk-actions', [
            'title' => $title,
            'assets' => $assets,
            'action' => $action,
        ]);
    }

    /**
     * Process bulk actions
     */
    public function processBulkActions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:verification,validation',
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'exists:assets,asset_id',
            'status' => 'required|in:valid,tidak_valid,disetujui,revisi,ditolak',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            foreach ($request->asset_ids as $assetId) {
                try {
                    $asset = Asset::findOrFail($assetId);
                    
                    if ($request->action === 'verification') {
                        $this->assetService->verifyDocument(
                            $asset,
                            $request->status,
                            $request->notes
                        );
                    } else {
                        $this->assetService->validateAsset(
                            $asset,
                            $request->status,
                            $request->notes
                        );
                    }
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Asset {$assetId}: " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            $message = "Berhasil memproses {$successCount} aset";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} gagal";
                session()->flash('errors', $errors);
            }
            
            $route = $request->action === 'verification' 
                ? 'admin.assets.index' 
                : 'admin.assets.index';
            
            return redirect()
                ->route($route, ['type' => 'pending-' . $request->action])
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.assets.bulk-actions', ['action' => $request->action])
                ->with('error', 'Gagal memproses massal: ' . $e->getMessage());
        }
    }

    /**
     * Export assets
     */
    public function export(Request $request)
    {
        $filters = $request->only([
            'opd_unit_id', 'status', 'category_id', 'condition',
            'document_verification_status', 'validation_status'
        ]);
        
        $assets = Asset::with(['category', 'opdUnit', 'location'])
            ->when($filters['opd_unit_id'] ?? false, function ($query, $opdUnitId) {
                return $query->where('opd_unit_id', $opdUnitId);
            })
            ->when($filters['status'] ?? false, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($filters['category_id'] ?? false, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->get();
        
        // For view export - can be extended to Excel/PDF
        return view('admin.assets.export', [
            'title' => 'Export Data Aset',
            'assets' => $assets,
            'filters' => $filters,
        ]);
    }
}