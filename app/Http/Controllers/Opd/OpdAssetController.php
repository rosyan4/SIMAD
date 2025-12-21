<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Services\AssetService;
use App\Services\AssetCodeService;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Document;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OpdAssetController extends Controller
{
    private AssetService $assetService;
    private AssetCodeService $codeService;

    public function __construct(
        AssetService $assetService,
        AssetCodeService $codeService
    ) {
        $this->middleware(['auth', 'admin.opd']);
        $this->assetService = $assetService;
        $this->codeService = $codeService;
    }

    /**
     * Display assets for OPD dengan berbagai view mode
     */
    public function index(Request $request)
    {
        $opdUnitId = auth()->user()->opd_unit_id;
        $perPage = $request->get('per_page', 20);
        
        $filters = $request->only([
            'status', 'category_id', 'condition',
            'document_verification_status', 'validation_status', 'search'
        ]);
        $filters['opd_unit_id'] = $opdUnitId;
        
        $assets = $this->assetService->searchAssets($filters, $perPage);
        $categories = Category::orderBy('kib_code')->get();
        $locations = Location::where('opd_unit_id', $opdUnitId)->get();
        
        $viewMode = $request->get('view', 'list'); // list, grid, map
        
        return view('opd.assets.index', [
            'title' => 'Daftar Aset',
            'assets' => $assets,
            'categories' => $categories,
            'locations' => $locations,
            'filters' => $filters,
            'viewMode' => $viewMode,
        ]);
    }

    /**
     * Show form for creating asset
     */
    public function create()
    {
        $opdUnitId = auth()->user()->opd_unit_id;
        
        $categories = Category::orderBy('kib_code')->get();
        $locations = Location::where('opd_unit_id', $opdUnitId)
            ->orderBy('name')
            ->get();
        
        return view('opd.assets.form', [
            'title' => 'Tambah Aset Baru',
            'categories' => $categories,
            'locations' => $locations,
            'opdUnit' => auth()->user()->opdUnit,
            'asset' => null,
        ]);
    }

    /**
     * Store a newly created asset
     */
    public function store(Request $request)
    {
        $request->validate(Asset::rules());
        
        try {
            $data = $request->all();
            $data['opd_unit_id'] = auth()->user()->opd_unit_id;
            $data['created_by'] = auth()->id();
            $data['document_verification_status'] = 'belum_diverifikasi';
            $data['validation_status'] = 'belum_divalidasi';
            $data['status'] = 'aktif';
            
            // Auto-generate asset code if not provided
            if (empty($data['asset_code'])) {
                $category = Category::find($data['category_id']);
                if ($category) {
                    $opdUnit = auth()->user()->opdUnit;
                    $data['asset_code'] = $this->codeService->generateAssetCode(
                        $category->kib_code,
                        $data['sub_category_code'],
                        $data['acquisition_year'] ?? date('Y'),
                        $opdUnit->kode_opd_numeric
                    );
                }
            }
            
            $asset = $this->assetService->createAsset($data, auth()->id());
            
            // Handle file uploads
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('asset-documents', 'public');
                    
                    Document::create([
                        'asset_id' => $asset->asset_id,
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension(),
                        'document_type' => $request->input('document_type', 'pengadaan'),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }
            
            return redirect()
                ->route('opd.assets.show', $asset)
                ->with('success', 'Aset berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan aset: ' . $e->getMessage());
        }
    }

    /**
     * Display asset detail dengan tab system
     */
    public function show(Asset $asset, Request $request)
    {
        $this->authorize('view', $asset);
        $tab = $request->get('tab', 'overview');
        
        $asset->load([
            'category', 'location', 'creator',
            'documents.uploader', 'histories.changer',
            'maintenances', 'depreciations', 'mutations', 'deletions'
        ]);
        
        // Tambahkan lokasi untuk dropdown pindah lokasi
        $opdUnitId = auth()->user()->opd_unit_id;
        $locations = Location::where('opd_unit_id', $opdUnitId)->get();
        
        $additionalData = [];
        if ($tab === 'documents') {
            $additionalData['documentTypes'] = [
                'pengadaan', 'mutasi', 'penghapusan',
                'pemeliharaan', 'lainnya'
            ];
        }
        
        return view('opd.assets.show', [
            'title' => 'Detail Aset: ' . $asset->name,
            'asset' => $asset,
            'tab' => $tab,
            'additionalData' => $additionalData,
            'locations' => $locations,
        ]);
    }

    /**
     * Show the form for editing asset
     */
    public function edit(Asset $asset)
    {
        $this->authorize('update', $asset);
        
        // Check if asset can be edited
        if (!$asset->canBeEditedByOPD()) {
            return redirect()
                ->route('opd.assets.show', $asset)
                ->with('error', 'Aset tidak dapat diedit karena sudah diverifikasi/divalidasi');
        }
        
        $opdUnitId = auth()->user()->opd_unit_id;
        $categories = Category::orderBy('kib_code')->get();
        $locations = Location::where('opd_unit_id', $opdUnitId)
            ->orderBy('name')
            ->get();
            
        return view('opd.assets.form', [
            'title' => 'Edit Aset: ' . $asset->name,
            'asset' => $asset,
            'categories' => $categories,
            'locations' => $locations,
        ]);
    }

    /**
     * Update asset
     */
    public function update(Request $request, Asset $asset)
    {
        $this->authorize('update', $asset);
        
        // Check if asset can be edited
        if (!$asset->canBeEditedByOPD()) {
            return redirect()
                ->route('opd.assets.show', $asset)
                ->with('error', 'Aset tidak dapat diedit karena sudah diverifikasi/divalidasi');
        }
        
        $request->validate(Asset::rules($asset->asset_id));
        
        try {
            $this->assetService->updateAsset($asset, $request->all());
            
            return redirect()
                ->route('opd.assets.show', $asset)
                ->with('success', 'Data aset berhasil diperbarui');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui aset: ' . $e->getMessage());
        }
    }

    /**
     * Delete asset (soft delete)
     */
    public function destroy(Asset $asset)
    {
        $this->authorize('delete', $asset);
        
        // Check if asset can be deleted
        if (!$asset->canBeDeletedByOPD()) {
            return redirect()
                ->back()
                ->with('error', 'Aset tidak dapat dihapus karena sudah diverifikasi/divalidasi. Gunakan proses penghapusan resmi.');
        }
        
        try {
            $this->assetService->deleteAsset($asset);
            
            return redirect()
                ->route('opd.assets.index')
                ->with('success', 'Aset berhasil dihapus');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus aset: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Preview asset code
     */
    public function previewAssetCode(Request $request)
    {
        $request->validate([
            'kib_code' => 'required|in:A,B,C,D,E,F',
            'sub_category_code' => 'required|string|size:2',
            'acquisition_year' => 'required|numeric|min:1900|max:' . date('Y'),
        ]);
        
        try {
            $opdUnit = auth()->user()->opdUnit;
            $code = $this->codeService->previewAssetCode(
                $request->kib_code,
                $request->sub_category_code,
                $request->acquisition_year,
                $opdUnit->kode_opd_numeric
            );
            
            return response()->json([
                'success' => true,
                'asset_code' => $code,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * AJAX: Update asset field (status/condition)
     */
    public function updateField(Request $request, Asset $asset)
    {
        $this->authorize('update', $asset);
        
        $request->validate([
            'field' => 'required|in:status,condition,location_id',
            'value' => 'required',
            'notes' => 'nullable|string|max:500',
        ]);
        
        try {
            $field = $request->field;
            $value = $request->value;
            
            if ($field === 'status') {
                $this->assetService->changeStatus($asset, $value, $request->notes);
            } elseif ($field === 'condition') {
                $oldValue = $asset->condition;
                $asset->update(['condition' => $value]);
                
                AssetHistory::create([
                    'asset_id' => $asset->asset_id,
                    'action' => 'update',
                    'description' => "Kondisi aset diubah dari {$oldValue} ke {$value}. " .
                        ($request->notes ? "Catatan: {$request->notes}" : ''),
                    'change_by' => auth()->id(),
                    'ip_address' => request()->ip(),
                ]);
            } elseif ($field === 'location_id') {
                $oldLocation = $asset->location;
                $newLocation = Location::find($value);
                
                if ($newLocation && $newLocation->opd_unit_id === auth()->user()->opd_unit_id) {
                    $asset->update(['location_id' => $value]);
                    
                    AssetHistory::create([
                        'asset_id' => $asset->asset_id,
                        'action' => 'update',
                        'description' => "Aset dipindahkan dari lokasi " .
                            ($oldLocation->name ?? 'Tidak ada') .
                            " ke " . $newLocation->name . ". " .
                            ($request->notes ? "Catatan: {$request->notes}" : ''),
                        'change_by' => auth()->id(),
                        'ip_address' => request()->ip(),
                    ]);
                } else {
                    throw new \Exception('Lokasi tidak valid');
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => ucfirst(str_replace('_', ' ', $field)) . ' berhasil diperbarui',
                'asset' => $asset->fresh(),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * AJAX: Upload document for asset
     */
    public function uploadDocument(Request $request, Asset $asset)
    {
        $this->authorize('update', $asset);
        
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
            'document_type' => 'required|in:pengadaan,mutasi,penghapusan,pemeliharaan,lainnya',
            'description' => 'nullable|string|max:255',
        ]);
        
        try {
            $file = $request->file('document');
            $path = $file->store('asset-documents', 'public');
            
            $document = Document::create([
                'asset_id' => $asset->asset_id,
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
                'document_type' => $request->document_type,
                'description' => $request->description,
                'uploaded_by' => auth()->id(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diupload',
                'document' => $document->load('uploader'),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload dokumen: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * AJAX: Delete document
     */
    public function deleteDocument(Document $document)
    {
        if ($document->asset->opd_unit_id !== auth()->user()->opd_unit_id) {
            abort(403, 'Dokumen tidak ditemukan');
        }
        
        try {
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            $document->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * AJAX: Get asset statistics
     */
    public function getStats()
    {
        $opdUnitId = auth()->user()->opd_unit_id;
        
        $stats = $this->assetService->getStatistics($opdUnitId);
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Export assets to Excel/PDF
     */
    public function export(Request $request)
    {
        $opdUnitId = auth()->user()->opd_unit_id;
        $format = $request->get('format', 'excel');
        $filters = $request->only(['status', 'category_id', 'condition']);
        $filters['opd_unit_id'] = $opdUnitId;
        
        $assets = $this->assetService->searchAssets($filters, 1000);
        
        // Here you would implement export logic using Laravel Excel or similar
        
        return response()->json([
            'success' => true,
            'message' => 'Export feature will be implemented',
            'count' => $assets->count(),
        ]);
    }
}