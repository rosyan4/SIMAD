<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Services\AssetService;
use App\Services\AssetCodeService;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
     * Store a newly created asset - VERSI DIPERBAIKI
     */
    public function store(Request $request)
    {
        Log::info('=== STORE ASSET START ===');
        Log::info('User Info:', [
            'id' => Auth::id(),
            'opd_unit_id' => Auth::user()->opd_unit_id
        ]);
        
        // Validasi tanpa field yang akan di-set otomatis
        $customRules = Asset::rules();
        
        // Hapus validasi untuk field yang akan di-set otomatis
        unset($customRules['status']);
        unset($customRules['document_verification_status']);
        unset($customRules['validation_status']);
        unset($customRules['opd_unit_id']);
        unset($customRules['created_by']);
        
        Log::info('Custom validation rules:', $customRules);
        Log::info('Request data:', $request->all());
        
        try {
            $request->validate($customRules);
            Log::info('Validation passed');
            
            // Hanya ambil field yang diperlukan dari request
            $data = $request->only([
                'name', 'category_id', 'sub_category_code', 
                'location_id', 'value', 'acquisition_year',
                'condition', 'kib_data', 'asset_code'
            ]);
            
            Log::info('Data after only():', $data);
            
            // Set field yang otomatis
            $data['opd_unit_id'] = Auth::user()->opd_unit_id;
            $data['created_by'] = Auth::id();
            $data['document_verification_status'] = 'belum_diverifikasi';
            $data['validation_status'] = 'belum_divalidasi';
            $data['status'] = 'aktif';
            
            Log::info('Data after setting defaults:', $data);
            
            // Handle KIB data dari form
            if ($request->has('kib_data')) {
                $kibData = [];
                foreach ($request->input('kib_data') as $key => $value) {
                    if (!empty($value)) {
                        $kibData[$key] = $value;
                    }
                }
                $data['kib_data'] = !empty($kibData) ? $kibData : null;
                Log::info('KIB Data processed:', $data['kib_data'] ?? []);
            }
            
            // Biarkan service yang generate asset code
            $asset = $this->assetService->createAsset($data, Auth::id());
            
            Log::info('Asset created successfully:', [
                'id' => $asset->asset_id,
                'code' => $asset->asset_code
            ]);
            
            // Handle file uploads
            if ($request->hasFile('documents')) {
                Log::info('Processing document uploads...');
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('asset-documents', 'public');
                    
                    Document::create([
                        'asset_id' => $asset->asset_id,
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension(),
                        'document_type' => $request->input('document_type', 'pengadaan'),
                        'uploaded_by' => Auth::id(),
                    ]);
                    
                    Log::info('Document uploaded:', ['path' => $path]);
                }
            }
            
            Log::info('=== STORE ASSET SUCCESS ===');
            
            return redirect()
                ->route('opd.assets.show', $asset)
                ->with('success', 'Aset berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            Log::error('=== STORE ASSET ERROR ===');
            Log::error('Error message: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            Log::error('Request data on error:', $request->all());
            
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
        
        if ($asset->document_verification_status === 'valid' ||
            $asset->validation_status === 'disetujui') {
            return redirect()
                ->route('opd.assets.show', $asset)
                ->with('error', 'Aset yang sudah diverifikasi/divalidasi tidak dapat diedit');
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
     * Update asset - VERSI DIPERBAIKI
     */
    public function update(Request $request, Asset $asset)
    {
        $this->authorize('update', $asset);
        
        if ($asset->document_verification_status === 'valid' ||
            $asset->validation_status === 'disetujui') {
            return redirect()
                ->route('opd.assets.show', $asset)
                ->with('error', 'Aset yang sudah diverifikasi/divalidasi tidak dapat diedit');
        }
        
        // Validasi tanpa field yang akan di-set otomatis
        $customRules = Asset::rules($asset->asset_id);
        
        // Hapus validasi untuk field yang akan di-set otomatis
        unset($customRules['status']);
        unset($customRules['document_verification_status']);
        unset($customRules['validation_status']);
        unset($customRules['opd_unit_id']);
        unset($customRules['created_by']);
        
        $request->validate($customRules);
        
        try {
            // Hanya ambil field yang boleh di-update
            $data = $request->only([
                'name', 'category_id', 'sub_category_code', 
                'location_id', 'value', 'acquisition_year',
                'condition', 'kib_data'
            ]);
            
            // Handle KIB data
            if ($request->has('kib_data')) {
                $kibData = [];
                foreach ($request->input('kib_data') as $key => $value) {
                    if (!empty($value)) {
                        $kibData[$key] = $value;
                    }
                }
                $data['kib_data'] = !empty($kibData) ? $kibData : null;
            }
            
            $this->assetService->updateAsset($asset, $data);
            
            return redirect()
                ->route('opd.assets.show', $asset)
                ->with('success', 'Data aset berhasil diperbarui');
                
        } catch (\Exception $e) {
            Log::error('Error updating asset: ' . $e->getMessage());
            
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
        
        if ($asset->document_verification_status === 'valid' ||
            $asset->validation_status === 'disetujui') {
            return redirect()
                ->back()
                ->with('error', 'Aset yang sudah diverifikasi/divalidasi tidak dapat dihapus. Gunakan proses penghapusan resmi.');
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
                
                $asset->createHistoryRecord(
                    'update',
                    "Kondisi aset diubah dari {$oldValue} ke {$value}. " .
                    ($request->notes ? "Catatan: {$request->notes}" : '')
                );
            } elseif ($field === 'location_id') {
                $oldLocation = $asset->location;
                $newLocation = Location::find($value);
                
                if ($newLocation && $newLocation->opd_unit_id === auth()->user()->opd_unit_id) {
                    $asset->update(['location_id' => $value]);
                    
                    $asset->createHistoryRecord(
                        'update',
                        "Aset dipindahkan dari lokasi " .
                        ($oldLocation->name ?? 'Tidak ada') .
                        " ke " . $newLocation->name . ". " .
                        ($request->notes ? "Catatan: {$request->notes}" : '')
                    );
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
        
        $assets = $this->assetService->searchAssets($filters, 1000); // Get all
        
        // Here you would implement export logic using Laravel Excel or similar
        
        return response()->json([
            'success' => true,
            'message' => 'Export feature will be implemented',
            'count' => $assets->count(),
        ]);
    }
    
    /**
     * TEST METHOD: Untuk debugging saja
     */
    public function storeTest(Request $request)
    {
        try {
            Log::info('=== STORE TEST START ===');
            
            // Test langsung tanpa validation
            $asset = Asset::create([
                'name' => $request->name ?: 'Test Asset ' . time(),
                'category_id' => $request->category_id ?: 1,
                'sub_category_code' => $request->sub_category_code ?: '01',
                'value' => $request->value ?: 1000000,
                'acquisition_year' => $request->acquisition_year ?: 2024,
                'status' => 'aktif',
                'condition' => $request->condition ?: 'Baik',
                'document_verification_status' => 'belum_diverifikasi',
                'validation_status' => 'belum_divalidasi',
                'opd_unit_id' => Auth::user()->opd_unit_id,
                'created_by' => Auth::id(),
                'asset_code' => 'TEST-' . time()
            ]);
            
            Log::info('Test asset created:', ['id' => $asset->asset_id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Test asset created successfully',
                'asset' => [
                    'id' => $asset->asset_id,
                    'name' => $asset->name,
                    'code' => $asset->asset_code
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test error: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}