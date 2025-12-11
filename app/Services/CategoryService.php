<?php

namespace App\Services;

use App\Models\Category;
use App\Models\OpdUnit;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    /**
     * Get sub categories by KIB code with caching
     */
    public function getSubCategories(string $kibCode): array
    {
        $cacheKey = "sub_categories_{$kibCode}";
        
        return Cache::remember($cacheKey, 3600, function () use ($kibCode) {
            // First try to get from database
            $category = Category::where('kib_code', $kibCode)->first();
            
            if ($category && !empty($category->sub_categories)) {
                return $category->sub_categories;
            }

            // Fallback to static data
            return $this->getStaticSubCategories($kibCode);
        });
    }

    /**
     * Static sub categories data
     */
    private function getStaticSubCategories(string $kibCode): array
    {
        $staticSubCategories = [
            'A' => [
                '01' => 'Tanah Perkantoran',
                '02' => 'Tanah Fasilitas Umum',
                '03' => 'Tanah Lainnya'
            ],
            'B' => [
                '01' => 'Alat Berat',
                '02' => 'Alat Elektronik',
                '03' => 'Kendaraan Dinas',
                '04' => 'Peralatan Medis',
                '05' => 'Peralatan Olahraga',
                '06' => 'Furniture',
                '07' => 'Alat Laboratorium'
            ],
            'C' => [
                '01' => 'Gedung Kantor',
                '02' => 'Gedung Sekolah',
                '03' => 'Rumah Sakit',
                '04' => 'Gedung Olahraga (GOR)',
                '05' => 'Stadion'
            ],
            'D' => [
                '01' => 'Jalan Kota',
                '02' => 'Jembatan',
                '03' => 'Jaringan Irigasi',
                '04' => 'Jaringan Internet'
            ],
            'E' => [
                '01' => 'Koleksi Perpustakaan',
                '02' => 'Aset Lainnya'
            ],
            'F' => [
                '01' => 'Konstruksi Gedung',
                '02' => 'Konstruksi Jalan',
                '03' => 'Konstruksi Lainnya'
            ],
        ];

        return $staticSubCategories[$kibCode] ?? [];
    }

    /**
     * Get all KIB categories with display names
     */
    public function getKibCategories(): array
    {
        return [
            'A' => 'KIB A - Tanah',
            'B' => 'KIB B - Peralatan dan Mesin',
            'C' => 'KIB C - Gedung dan Bangunan',
            'D' => 'KIB D - Jalan, Irigasi, dan Jaringan',
            'E' => 'KIB E - Aset Tetap Lainnya',
            'F' => 'KIB F - Konstruksi Dalam Pengerjaan',
        ];
    }

    /**
     * Get OPD numeric code mapping
     */
    public function getKodeDinasNumericMapping(): array
    {
        return Cache::remember('opd_numeric_mapping', 3600, function () {
            return OpdUnit::all()->pluck('nama_opd', 'kode_opd_numeric')->toArray();
        });
    }

    /**
     * Get OPD name by numeric code
     */
    public function getDinasNameByNumeric(int $kodeDinasNumeric): string
    {
        $mapping = $this->getKodeDinasNumericMapping();
        return $mapping[$kodeDinasNumeric] ?? 'Dinas Tidak Dikenal';
    }

    /**
     * Get category statistics
     */
    public function getCategoryStatistics(): array
    {
        return Cache::remember('category_statistics', 1800, function () {
            $categories = Category::withCount(['assets'])
                ->withSum('assets', 'value')
                ->get();

            $stats = [];
            foreach ($categories as $category) {
                $stats[] = [
                    'category_id' => $category->category_id,
                    'name' => $category->name,
                    'kib_code' => $category->kib_code,
                    'asset_count' => $category->assets_count,
                    'total_value' => (float) $category->assets_sum_value,
                    'formatted_value' => 'Rp ' . number_format($category->assets_sum_value, 0, ',', '.'),
                ];
            }

            return $stats;
        });
    }

    /**
     * Create or update category with sub categories
     */
    public function saveCategory(array $data, ?int $categoryId = null): Category
    {
        $category = $categoryId ? Category::findOrFail($categoryId) : new Category();

        // Validate KIB code
        if (!in_array($data['kib_code'], Category::KIB_CODES)) {
            throw new \InvalidArgumentException('Kode KIB tidak valid');
        }

        // Validate sub categories format
        if (isset($data['sub_categories']) && !is_array($data['sub_categories'])) {
            throw new \InvalidArgumentException('Sub categories harus dalam format array');
        }

        $category->fill($data);
        $category->save();

        // Clear cache
        Cache::forget("sub_categories_{$category->kib_code}");
        Cache::forget('category_statistics');

        return $category;
    }
}