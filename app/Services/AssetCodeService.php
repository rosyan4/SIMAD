<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\OpdUnit;
use Illuminate\Support\Facades\DB;

class AssetCodeService
{
    /**
     * Generate asset code otomatis dengan kode dinas numerik
     * Format: KIB-SUBKATEGORI-TAHUN-KODEDINAS(numeric)-SEQ
     * Contoh: B-01-2025-05-001
     */
    public function generateAssetCode(string $kibCode, string $subCategory, int $year, int $kodeDinasNumeric): string
    {
        $this->validateInput($kibCode, $subCategory, $year, $kodeDinasNumeric);

        // Format kode dinas: 2 digit dengan leading zero
        $kodeDinasPadded = str_pad($kodeDinasNumeric, 2, '0', STR_PAD_LEFT);

        // Hitung sequence terakhir dengan LOCK
        $lastSequence = $this->getLastSequence($kibCode, $subCategory, $year, $kodeDinasPadded);

        $sequencePadded = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);

        return "{$kibCode}-{$subCategory}-{$year}-{$kodeDinasPadded}-{$sequencePadded}";
    }

    /**
     * Generate asset code dari Asset object
     */
    public function generateAssetCodeFromAsset(Asset $asset): string
    {
        $category = $asset->category;
        $opdUnit = $asset->opdUnit;
        
        if (!$category) {
            throw new \InvalidArgumentException('Asset must have a category');
        }
        
        if (!$opdUnit || !$opdUnit->kode_opd_numeric) {
            throw new \InvalidArgumentException('Asset must have an OPD unit with numeric code');
        }

        return $this->generateAssetCode(
            $category->kib_code,
            $asset->sub_category_code,
            $asset->acquisition_year,
            $opdUnit->kode_opd_numeric
        );
    }

    /**
     * Validate input parameters
     */
    private function validateInput(string $kibCode, string $subCategory, int $year, int $kodeDinasNumeric): void
    {
        // Validasi kode dinas numerik
        if ($kodeDinasNumeric <= 0 || $kodeDinasNumeric > 99) {
            throw new \InvalidArgumentException('Kode dinas harus angka positif antara 1-99');
        }

        // Validasi KIB code
        $allowedKibCodes = ['A', 'B', 'C', 'D', 'E', 'F'];
        if (!in_array($kibCode, $allowedKibCodes)) {
            throw new \InvalidArgumentException('Kode KIB tidak valid. Harus A, B, C, D, E, atau F');
        }

        // Validasi sub category (2 digit)
        if (!preg_match('/^\d{2}$/', $subCategory)) {
            throw new \InvalidArgumentException('Sub kategori harus 2 digit angka');
        }

        // Validasi tahun (4 digit)
        $currentYear = date('Y');
        if (!preg_match('/^\d{4}$/', (string)$year) || $year < 1900 || $year > $currentYear) {
            throw new \InvalidArgumentException("Tahun harus 4 digit antara 1900 dan {$currentYear}");
        }
    }

    /**
     * Get last sequence number
     */
    private function getLastSequence(string $kibCode, string $subCategory, int $year, string $kodeDinasPadded): int
    {
        $pattern = "{$kibCode}-{$subCategory}-{$year}-{$kodeDinasPadded}-%";

        return DB::transaction(function () use ($pattern) {
            $lastAsset = Asset::where('asset_code', 'LIKE', $pattern)
                ->orderBy('asset_code', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastAsset) {
                $parts = explode('-', $lastAsset->asset_code);
                if (count($parts) === 5) {
                    return (int) $parts[4];
                }
            }

            return 0;
        });
    }

    /**
     * Parse asset code menjadi komponen
     */
    public function parseAssetCode(string $assetCode): ?array
    {
        $parts = explode('-', $assetCode);
        
        if (count($parts) !== 5) {
            return null;
        }

        // Validasi setiap bagian
        if (!preg_match('/^[A-F]$/', $parts[0])) {
            return null;
        }

        if (!preg_match('/^\d{2}$/', $parts[1])) {
            return null;
        }

        if (!preg_match('/^\d{4}$/', $parts[2])) {
            return null;
        }

        if (!preg_match('/^\d{2}$/', $parts[3])) {
            return null;
        }

        if (!preg_match('/^\d{3}$/', $parts[4])) {
            return null;
        }

        return [
            'kib_code' => $parts[0],
            'sub_category' => $parts[1],
            'tahun' => $parts[2],
            'kode_dinas_numeric' => (int) $parts[3],
            'kode_dinas_padded' => $parts[3],
            'sequence' => (int) $parts[4],
        ];
    }

    /**
     * Validate asset code format
     */
    public function validateAssetCode(string $assetCode): bool
    {
        $parsed = $this->parseAssetCode($assetCode);
        
        if (!$parsed) {
            return false;
        }

        $kodeDinas = (int) $parsed['kode_dinas_padded'];
        if ($kodeDinas < 1 || $kodeDinas > 99) {
            return false;
        }

        return true;
    }

    /**
     * Preview code tanpa saving
     */
    public function previewAssetCode(string $kibCode, string $subCategory, int $year, int $kodeDinasNumeric): string
    {
        $kodeDinasPadded = str_pad($kodeDinasNumeric, 2, '0', STR_PAD_LEFT);
        $sequencePadded = str_pad(1, 3, '0', STR_PAD_LEFT);
        
        return "{$kibCode}-{$subCategory}-{$year}-{$kodeDinasPadded}-{$sequencePadded}";
    }

    /**
     * Migrasi kode lama ke format baru
     */
    public function migrateOldCodes(): array
    {
        $assets = Asset::all();
        $migrationLog = [];

        foreach ($assets as $asset) {
            try {
                $oldCode = $asset->asset_code;
                $parts = explode('-', $oldCode);
                
                if (count($parts) !== 5) {
                    $migrationLog[] = [
                        'asset_id' => $asset->asset_id,
                        'old_code' => $oldCode,
                        'error' => 'Format kode lama tidak valid',
                        'success' => false
                    ];
                    continue;
                }

                $opdUnit = $asset->opdUnit;
                if (!$opdUnit || !$opdUnit->kode_opd_numeric) {
                    $migrationLog[] = [
                        'asset_id' => $asset->asset_id,
                        'old_code' => $oldCode,
                        'error' => 'OPD unit tidak ditemukan',
                        'success' => false
                    ];
                    continue;
                }

                $newCode = $this->generateAssetCode(
                    $parts[0],
                    $parts[1],
                    $parts[2],
                    $opdUnit->kode_opd_numeric
                );

                $asset->asset_code_old = $oldCode;
                $asset->asset_code = $newCode;
                $asset->save();

                $migrationLog[] = [
                    'asset_id' => $asset->asset_id,
                    'old_code' => $oldCode,
                    'new_code' => $newCode,
                    'success' => true
                ];

            } catch (\Exception $e) {
                $migrationLog[] = [
                    'asset_id' => $asset->asset_id,
                    'old_code' => $oldCode ?? 'N/A',
                    'error' => $e->getMessage(),
                    'success' => false
                ];
            }
        }

        return $migrationLog;
    }

    /**
     * Generate asset code berdasarkan asset object
     */
    public function generateForAsset(Asset $asset): string
    {
        $category = $asset->category;
        $opdUnit = $asset->opdUnit;

        if (!$category || !$opdUnit) {
            throw new \InvalidArgumentException('Asset must have category and OPD unit');
        }

        return $this->generateAssetCode(
            $category->kib_code,
            $asset->sub_category_code,
            $asset->acquisition_year,
            $opdUnit->kode_opd_numeric
        );
    }

    /**
     * Get OPD numeric code by name or code
     */
    public function getOpdNumericCode(string $identifier): ?int
    {
        $opdUnit = OpdUnit::where('kode_opd', $identifier)
            ->orWhere('nama_opd', 'LIKE', "%{$identifier}%")
            ->first();

        return $opdUnit ? $opdUnit->kode_opd_numeric : null;
    }
}