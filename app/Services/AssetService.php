<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\Category;
use App\Models\OpdUnit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetService
{
    private AssetCodeService $codeService;
    private AssetValidatorService $validatorService;

    public function __construct(
        AssetCodeService $codeService,
        AssetValidatorService $validatorService
    ) {
        $this->codeService = $codeService;
        $this->validatorService = $validatorService;
    }

    /**
     * Create new asset with validation and code generation - DIKOREKSI
     */
    public function createAsset(array $data, int $userId): Asset
    {
        return DB::transaction(function () use ($data, $userId) {
            // Validate business rules
            $this->validatorService->validateAssetData($data);
            
            // Auto-generate asset code if not provided
            if (empty($data['asset_code'])) {
                // Dapatkan kategori untuk mengambil kib_code
                $category = Category::find($data['category_id']);
                if (!$category) {
                    throw new \InvalidArgumentException('Kategori tidak ditemukan');
                }
                
                // Dapatkan kode OPD numeric
                $opdUnit = OpdUnit::find($data['opd_unit_id']);
                if (!$opdUnit || !$opdUnit->kode_opd_numeric) {
                    throw new \InvalidArgumentException('OPD unit tidak ditemukan atau tidak memiliki kode numerik');
                }
                
                // Generate asset code menggunakan kib_code dari kategori
                $data['asset_code'] = $this->codeService->generateAssetCode(
                    $category->kib_code, // AMBIL DARI CATEGORY, bukan dari $data
                    $data['sub_category_code'],
                    $data['acquisition_year'],
                    $opdUnit->kode_opd_numeric
                );
            }

            // Set creator
            $data['created_by'] = $userId;

            // Create asset
            $asset = Asset::create($data);

            // Create initial history
            $this->createHistory($asset, 'create', 'Aset baru ditambahkan', null, $asset->toArray());

            return $asset;
        });
    }

    /**
     * Update existing asset
     */
    public function updateAsset(Asset $asset, array $data): Asset
    {
        return DB::transaction(function () use ($asset, $data) {
            $oldData = $asset->toArray();
            
            // Validate business rules
            $this->validatorService->validateAssetData($data);
            
            // Update asset
            $asset->update($data);
            
            // Create history record if changes detected
            if ($asset->wasChanged()) {
                $this->createHistory(
                    $asset,
                    'update',
                    'Data aset diperbarui',
                    array_intersect_key($oldData, $asset->getChanges()),
                    $asset->getChanges()
                );
            }

            return $asset;
        });
    }

    /**
     * Soft delete asset
     */
    public function deleteAsset(Asset $asset): bool
    {
        return DB::transaction(function () use ($asset) {
            $oldData = $asset->toArray();
            $deleted = $asset->delete();

            if ($deleted) {
                $this->createHistory(
                    $asset,
                    'delete',
                    'Aset dihapus (soft delete)',
                    $oldData,
                    null
                );
            }

            return $deleted;
        });
    }

    /**
     * Restore soft deleted asset
     */
    public function restoreAsset(Asset $asset): bool
    {
        return DB::transaction(function () use ($asset) {
            $restored = $asset->restore();

            if ($restored) {
                $this->createHistory(
                    $asset,
                    'restore',
                    'Aset dikembalikan dari penghapusan',
                    null,
                    $asset->toArray()
                );
            }

            return $restored;
        });
    }

    /**
     * Change asset status
     */
    public function changeStatus(Asset $asset, string $status, ?string $notes = null): Asset
    {
        if (!in_array($status, Asset::STATUSES)) {
            throw new \InvalidArgumentException('Status tidak valid');
        }

        return DB::transaction(function () use ($asset, $status, $notes) {
            $oldStatus = $asset->status;
            
            $asset->update(['status' => $status]);
            
            $description = "Status aset diubah dari {$oldStatus} ke {$status}";
            if ($notes) {
                $description .= " - Catatan: {$notes}";
            }

            $this->createHistory(
                $asset,
                'update',
                $description,
                ['status' => $oldStatus],
                ['status' => $status]
            );

            return $asset;
        });
    }

    /**
     * Verify asset document
     */
    public function verifyDocument(Asset $asset, string $status, ?string $notes = null): Asset
    {
        if (!in_array($status, Asset::DOCUMENT_VERIFICATION_STATUSES)) {
            throw new \InvalidArgumentException('Status verifikasi dokumen tidak valid');
        }

        return DB::transaction(function () use ($asset, $status, $notes) {
            $oldStatus = $asset->document_verification_status;
            
            $asset->update(['document_verification_status' => $status]);
            
            $description = "Verifikasi dokumen diubah dari {$oldStatus} ke {$status}";
            if ($notes) {
                $description .= " - Catatan: {$notes}";
            }

            $this->createHistory(
                $asset,
                'verifikasi',
                $description,
                ['document_verification_status' => $oldStatus],
                ['document_verification_status' => $status]
            );

            return $asset;
        });
    }

    /**
     * Validate asset
     */
    public function validateAsset(Asset $asset, string $status, ?string $notes = null): Asset
    {
        if (!in_array($status, Asset::VALIDATION_STATUSES)) {
            throw new \InvalidArgumentException('Status validasi tidak valid');
        }

        return DB::transaction(function () use ($asset, $status, $notes) {
            $oldStatus = $asset->validation_status;
            
            $asset->update(['validation_status' => $status]);
            
            $description = "Validasi aset diubah dari {$oldStatus} ke {$status}";
            if ($notes) {
                $description .= " - Catatan: {$notes}";
            }

            $this->createHistory(
                $asset,
                'validasi',
                $description,
                ['validation_status' => $oldStatus],
                ['validation_status' => $status]
            );

            return $asset;
        });
    }

    /**
     * Create history record
     */
    private function createHistory(
        Asset $asset,
        string $action,
        string $description,
        ?array $oldValue = null,
        ?array $newValue = null
    ): AssetHistory {
        return AssetHistory::create([
            'asset_id' => $asset->asset_id,
            'action' => $action,
            'description' => $description,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'change_by' => Auth::id() ?? $asset->created_by,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get OPD numeric code from OPD unit ID
     */
    private function getOpdNumericCode(int $opdUnitId): int
    {
        $opdUnit = OpdUnit::find($opdUnitId);
        
        if (!$opdUnit || !$opdUnit->kode_opd_numeric) {
            throw new \InvalidArgumentException('OPD unit tidak ditemukan atau tidak memiliki kode numerik');
        }

        return $opdUnit->kode_opd_numeric;
    }

    /**
     * Get asset statistics
     */
    public function getStatistics(?int $opdUnitId = null): array
    {
        $query = Asset::query();
        if ($opdUnitId) {
            $query->where('opd_unit_id', $opdUnitId);
        }

        $total = $query->count();
        $totalValue = $query->sum('value');
        $active = $query->clone()->where('status', 'aktif')->count();
        $verified = $query->clone()
            ->where('document_verification_status', 'valid')
            ->where('validation_status', 'disetujui')
            ->count();

        // Status distribution
        $statusDistribution = $query->clone()
            ->select('status', DB::raw('count(*) as count'), DB::raw('sum(value) as total_value'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => [
                    'count' => $item->count,
                    'total_value' => $item->total_value
                ]];
            })
            ->toArray();

        // Condition distribution
        $conditionDistribution = $query->clone()
            ->select('condition', DB::raw('count(*) as count'))
            ->groupBy('condition')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->condition => $item->count];
            })
            ->toArray();

        return [
            'total_assets' => $total,
            'total_value' => $totalValue,
            'active_assets' => $active,
            'verified_assets' => $verified,
            'verification_rate' => $total > 0 ? round(($verified / $total) * 100, 2) : 0,
            'status_distribution' => $statusDistribution,
            'condition_distribution' => $conditionDistribution,
        ];
    }

    /**
     * Search assets with filters
     */
    public function searchAssets(array $filters = [], int $perPage = 20)
    {
        $query = Asset::with(['category', 'opdUnit', 'location']);

        // Apply filters
        if (!empty($filters['opd_unit_id'])) {
            $query->where('opd_unit_id', $filters['opd_unit_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        if (!empty($filters['document_verification_status'])) {
            $query->where('document_verification_status', $filters['document_verification_status']);
        }

        if (!empty($filters['validation_status'])) {
            $query->where('validation_status', $filters['validation_status']);
        }

        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('asset_code', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('asset_code_old', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Sort
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }
}