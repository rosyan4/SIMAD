<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Category;
use App\Models\OpdUnit;
use App\Models\Location;

class AssetValidatorService
{
    /**
     * Validate all asset data
     */
    public function validateAssetData(array $data): void
    {
        if (isset($data['status']) && !in_array($data['status'], Asset::STATUSES)) {
            throw new \InvalidArgumentException("Invalid status value. Must be one of: " . implode(', ', Asset::STATUSES));
        }
        
        if (isset($data['condition']) && !in_array($data['condition'], Asset::CONDITIONS)) {
            throw new \InvalidArgumentException("Invalid condition value. Must be one of: " . implode(', ', Asset::CONDITIONS));
        }
        
        if (isset($data['document_verification_status']) && !in_array($data['document_verification_status'], Asset::DOCUMENT_VERIFICATION_STATUSES)) {
            throw new \InvalidArgumentException("Invalid document verification status. Must be one of: " . implode(', ', Asset::DOCUMENT_VERIFICATION_STATUSES));
        }
        
        if (isset($data['validation_status']) && !in_array($data['validation_status'], Asset::VALIDATION_STATUSES)) {
            throw new \InvalidArgumentException("Invalid validation status. Must be one of: " . implode(', ', Asset::VALIDATION_STATUSES));
        }

        // Validate acquisition year
        if (isset($data['acquisition_year'])) {
            $currentYear = date('Y');
            if ($data['acquisition_year'] < 1900 || $data['acquisition_year'] > $currentYear) {
                throw new \InvalidArgumentException("Acquisition year must be between 1900 and {$currentYear}");
            }
        }

        // Validate value
        if (isset($data['value']) && $data['value'] < 0) {
            throw new \InvalidArgumentException("Asset value cannot be negative");
        }

        // Validate foreign keys
        if (isset($data['category_id'])) {
            $this->validateCategory($data['category_id']);
        }

        if (isset($data['opd_unit_id'])) {
            $this->validateOpdUnit($data['opd_unit_id']);
        }

        if (isset($data['location_id']) && $data['location_id']) {
            $this->validateLocation($data['location_id'], $data['opd_unit_id'] ?? null);
        }

        // Validate sub category exists in category
        if (isset($data['category_id']) && isset($data['sub_category_code'])) {
            $this->validateSubCategory($data['category_id'], $data['sub_category_code']);
        }
    }

    /**
     * Validate category exists
     */
    private function validateCategory(int $categoryId): void
    {
        $category = Category::find($categoryId);
        
        if (!$category) {
            throw new \InvalidArgumentException("Category with ID {$categoryId} not found");
        }
    }

    /**
     * Validate OPD unit exists
     */
    private function validateOpdUnit(int $opdUnitId): void
    {
        $opdUnit = OpdUnit::find($opdUnitId);
        
        if (!$opdUnit) {
            throw new \InvalidArgumentException("OPD unit with ID {$opdUnitId} not found");
        }
    }

    /**
     * Validate location exists and belongs to correct OPD
     */
    private function validateLocation(int $locationId, ?int $opdUnitId): void
    {
        $location = Location::find($locationId);
        
        if (!$location) {
            throw new \InvalidArgumentException("Location with ID {$locationId} not found");
        }

        // Validate location belongs to the same OPD
        if ($opdUnitId && $location->opd_unit_id != $opdUnitId) {
            throw new \InvalidArgumentException("Location does not belong to the specified OPD unit");
        }
    }

    /**
     * Validate sub category exists in category
     */
    private function validateSubCategory(int $categoryId, string $subCategoryCode): void
    {
        $category = Category::find($categoryId);
        
        if (!$category) {
            throw new \InvalidArgumentException("Category not found");
        }

        $subCategories = $category->sub_categories ?? [];
        
        if (!array_key_exists($subCategoryCode, $subCategories)) {
            throw new \InvalidArgumentException("Sub category code '{$subCategoryCode}' not found in category");
        }
    }

    /**
     * Validate KIB data structure based on KIB code
     */
    public function validateKibData(string $kibCode, array $kibData): void
    {
        switch ($kibCode) {
            case 'A': // Tanah
                $required = ['luas', 'lokasi_tanah', 'status_hak', 'sertifikat_tanggal', 'sertifikat_nomor'];
                break;
            case 'B': // Peralatan dan Mesin
                $required = ['merk', 'tipe', 'spesifikasi', 'bahan', 'tahun_pembuatan'];
                break;
            case 'C': // Gedung dan Bangunan
                $required = ['luas_bangunan', 'jumlah_lantai', 'konstruksi', 'alamat_lengkap'];
                break;
            case 'D': // Jalan, Irigasi, dan Jaringan
                $required = ['panjang', 'lebar', 'konstruksi', 'kondisi'];
                break;
            case 'E': // Aset Tetap Lainnya
                $required = ['jenis', 'spesifikasi', 'kondisi'];
                break;
            case 'F': // Konstruksi Dalam Pengerjaan
                $required = ['nama_kontraktor', 'nilai_kontrak', 'tanggal_mulai', 'tanggal_selesai'];
                break;
            default:
                throw new \InvalidArgumentException("Invalid KIB code: {$kibCode}");
        }

        foreach ($required as $field) {
            if (!isset($kibData[$field]) || empty($kibData[$field])) {
                throw new \InvalidArgumentException("KIB data field '{$field}' is required for KIB {$kibCode}");
            }
        }
    }

    /**
     * Validate asset code format
     */
    public function validateAssetCodeFormat(string $assetCode): bool
    {
        $parts = explode('-', $assetCode);
        
        if (count($parts) !== 5) {
            return false;
        }

        // Validate KIB code
        if (!in_array($parts[0], ['A', 'B', 'C', 'D', 'E', 'F'])) {
            return false;
        }

        // Validate sub category (2 digits)
        if (!preg_match('/^\d{2}$/', $parts[1])) {
            return false;
        }

        // Validate year (4 digits)
        if (!preg_match('/^\d{4}$/', $parts[2])) {
            return false;
        }

        // Validate OPD code (2 digits)
        if (!preg_match('/^\d{2}$/', $parts[3])) {
            return false;
        }

        // Validate sequence (3 digits)
        if (!preg_match('/^\d{3}$/', $parts[4])) {
            return false;
        }

        return true;
    }
}