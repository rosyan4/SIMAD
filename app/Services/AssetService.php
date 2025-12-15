<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\Category;
use App\Models\OpdUnit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * Create new asset with validation and code generation - VERSI DIPERBAIKI
     */
    public function createAsset(array $data, int $userId): Asset
    {
        Log::info('=== ASSET SERVICE CREATE START ===');
        Log::info('Input data keys:', array_keys($data));
        Log::info('User ID:', ['user_id' => $userId]);
        
        return DB::transaction(function () use ($data, $userId) {
            Log::info('Transaction started for asset creation');
            
            // Ensure required fields are set
            $defaultFields = [
                'status' => 'aktif',
                'document_verification_status' => 'belum_diverifikasi',
                'validation_status' => 'belum_divalidasi',
                'created_by' => $userId
            ];
            
            foreach ($defaultFields as $field => $defaultValue) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    $data[$field] = $defaultValue;
                    Log::info("Set default for {$field}: {$defaultValue}");
                }
            }
            
            // Validate OPD unit exists
            if (!isset($data['opd_unit_id'])) {
                Log::error('Missing opd_unit_id in data');
                throw new \InvalidArgumentException('OPD unit ID tidak ditemukan');
            }
            
            // Validate business rules
            try {
                $this->validatorService->validateAssetData($data);
                Log::info('Asset data validation passed');
            } catch (\Exception $e) {
                Log::error('Asset validation failed: ' . $e->getMessage());
                throw $e;
            }
            
            // Auto-generate asset code if not provided
            if (empty($data['asset_code'])) {
                Log::info('Generating asset code...');
                
                // Get category for kib_code
                $category = Category::find($data['category_id']);
                if (!$category) {
                    Log::error('Category not found:', ['category_id' => $data['category_id']]);
                    throw new \InvalidArgumentException('Kategori tidak ditemukan');
                }

                // Get OPD numeric code
                $opdUnit = OpdUnit::find($data['opd_unit_id']);
                if (!$opdUnit) {
                    Log::error('OPD unit not found:', ['opd_unit_id' => $data['opd_unit_id']]);
                    throw new \InvalidArgumentException('OPD unit tidak ditemukan');
                }
                
                if (!$opdUnit->kode_opd_numeric) {
                    Log::error('OPD unit missing numeric code:', ['opd_unit_id' => $opdUnit->opd_unit_id]);
                    throw new \InvalidArgumentException('OPD unit tidak memiliki kode numerik');
                }

                try {
                    $data['asset_code'] = $this->codeService->generateAssetCode(
                        $category->kib_code,
                        $data['sub_category_code'],
                        $data['acquisition_year'],
                        $opdUnit->kode_opd_numeric
                    );
                    Log::info('Asset code generated:', ['asset_code' => $data['asset_code']]);
                } catch (\Exception $e) {
                    Log::error('Failed to generate asset code: ' . $e->getMessage());
                    throw new \InvalidArgumentException('Gagal generate kode aset: ' . $e->getMessage());
                }
            } else {
                Log::info('Using provided asset code:', ['asset_code' => $data['asset_code']]);
            }
            
            // Ensure KIB data is properly formatted
            if (isset($data['kib_data']) && is_array($data['kib_data'])) {
                // Filter out empty values
                $data['kib_data'] = array_filter($data['kib_data'], function($value) {
                    return $value !== null && $value !== '';
                });
                
                if (empty($data['kib_data'])) {
                    $data['kib_data'] = null;
                }
            } else {
                $data['kib_data'] = null;
            }
            
            Log::info('Final data before creating asset:', [
                'fields' => array_keys($data),
                'has_kib_data' => !empty($data['kib_data'])
            ]);
            
            // Create asset
            try {
                $asset = Asset::create($data);
                
                if (!$asset) {
                    Log::error('Asset::create() returned null');
                    throw new \Exception('Gagal membuat aset - create() returned null');
                }
                
                Log::info('Asset created successfully:', [
                    'asset_id' => $asset->asset_id,
                    'asset_code' => $asset->asset_code,
                    'name' => $asset->name
                ]);
                
                // Create initial history
                $history = $this->createHistory($asset, 'create', 'Aset baru ditambahkan', null, $asset->toArray());
                Log::info('History record created:', ['history_id' => $history->history_id]);
                
                Log::info('=== ASSET SERVICE CREATE SUCCESS ===');
                return $asset;
                
            } catch (\Exception $e) {
                Log::error('Failed to create asset in database: ' . $e->getMessage());
                Log::error('Database error code: ' . $e->getCode());
                throw new \Exception('Gagal menyimpan aset ke database: ' . $e->getMessage());
            }
        });
    }

    /**
     * Update existing asset - VERSI DIPERBAIKI
     */
    public function updateAsset(Asset $asset, array $data): Asset
    {
        return DB::transaction(function () use ($asset, $data) {
            $oldData = $asset->toArray();
            
            // Validate business rules
            $this->validatorService->validateAssetData($data);
            
            // Ensure KIB data is properly formatted
            if (isset($data['kib_data']) && is_array($data['kib_data'])) {
                $data['kib_data'] = array_filter($data['kib_data'], function($value) {
                    return $value !== null && $value !== '';
                });
                
                if (empty($data['kib_data'])) {
                    $data['kib_data'] = null;
                }
            }
            
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
            'change_date' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
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