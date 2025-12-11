<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\OpdUnit;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Hapus semua data
        User::truncate();
        OpdUnit::truncate();
        Category::truncate();
        Location::truncate();

        // 1. Buat OPD Units
        $opdUnits = [
            [
                'kode_opd' => 'DINPORA',
                'kode_opd_numeric' => 1,
                'nama_opd' => 'Dinas Pemuda dan Olahraga',
                'alamat' => 'Jl. Olahraga No. 1',
                'kepala_opd' => 'Dr. Ahmad Santoso',
                'nip_kepala_opd' => '196512341987031001',
            ],
            [
                'kode_opd' => 'DINPEN',
                'kode_opd_numeric' => 2,
                'nama_opd' => 'Dinas Pendidikan',
                'alamat' => 'Jl. Pendidikan No. 2',
                'kepala_opd' => 'Dra. Siti Rahayu',
                'nip_kepala_opd' => '196712341989022001',
            ],
            [
                'kode_opd' => 'DINKES',
                'kode_opd_numeric' => 3,
                'nama_opd' => 'Dinas Kesehatan',
                'alamat' => 'Jl. Kesehatan No. 3',
                'kepala_opd' => 'Dr. Budi Hartono',
                'nip_kepala_opd' => '197012341992031002',
            ],
            [
                'kode_opd' => 'DINPUM',
                'kode_opd_numeric' => 4,
                'nama_opd' => 'Dinas Pemberdayaan Masyarakat',
                'alamat' => 'Jl. Masyarakat No. 4',
                'kepala_opd' => 'Drs. Joko Widodo',
                'nip_kepala_opd' => '197212341994031003',
            ],
            [
                'kode_opd' => 'DINPAR',
                'kode_opd_numeric' => 5,
                'nama_opd' => 'Dinas Pariwisata',
                'alamat' => 'Jl. Pariwisata No. 5',
                'kepala_opd' => 'Ir. Sri Mulyani',
                'nip_kepala_opd' => '197412341996022002',
            ],
            [
                'kode_opd' => 'BPKAD',
                'kode_opd_numeric' => 10,
                'nama_opd' => 'Badan Pengelola Keuangan dan Aset Daerah',
                'alamat' => 'Jl. Keuangan No. 10',
                'kepala_opd' => 'Prof. Dr. Bambang Brodjonegoro',
                'nip_kepala_opd' => '196812341990031004',
            ],
        ];

        foreach ($opdUnits as $opd) {
            OpdUnit::create($opd);
        }

        // 2. Buat Kategori
        $categories = [
            [
                'name' => 'Tanah',
                'description' => 'KIB A - Tanah',
                'standard_code_ref' => 'KIB-A-001',
                'kib_code' => 'A',
                'sub_categories' => [
                    '01' => 'Tanah Perkantoran',
                    '02' => 'Tanah Fasilitas Umum',
                    '03' => 'Tanah Lainnya'
                ]
            ],
            [
                'name' => 'Peralatan dan Mesin',
                'description' => 'KIB B - Peralatan dan Mesin',
                'standard_code_ref' => 'KIB-B-001',
                'kib_code' => 'B',
                'sub_categories' => [
                    '01' => 'Alat Berat',
                    '02' => 'Alat Elektronik',
                    '03' => 'Kendaraan Dinas',
                    '04' => 'Peralatan Medis',
                    '05' => 'Peralatan Olahraga',
                    '06' => 'Furniture',
                    '07' => 'Alat Laboratorium'
                ]
            ],
            [
                'name' => 'Gedung dan Bangunan',
                'description' => 'KIB C - Gedung dan Bangunan',
                'standard_code_ref' => 'KIB-C-001',
                'kib_code' => 'C',
                'sub_categories' => [
                    '01' => 'Gedung Kantor',
                    '02' => 'Gedung Sekolah',
                    '03' => 'Rumah Sakit',
                    '04' => 'Gedung Olahraga (GOR)',
                    '05' => 'Stadion'
                ]
            ],
            [
                'name' => 'Jalan, Irigasi, dan Jaringan',
                'description' => 'KIB D - Jalan, Irigasi, dan Jaringan',
                'standard_code_ref' => 'KIB-D-001',
                'kib_code' => 'D',
                'sub_categories' => [
                    '01' => 'Jalan Kota',
                    '02' => 'Jembatan',
                    '03' => 'Jaringan Irigasi',
                    '04' => 'Jaringan Internet'
                ]
            ],
            [
                'name' => 'Aset Tetap Lainnya',
                'description' => 'KIB E - Aset Tetap Lainnya',
                'standard_code_ref' => 'KIB-E-001',
                'kib_code' => 'E',
                'sub_categories' => [
                    '01' => 'Koleksi Perpustakaan',
                    '02' => 'Aset Lainnya'
                ]
            ],
            [
                'name' => 'Konstruksi Dalam Pengerjaan',
                'description' => 'KIB F - Konstruksi Dalam Pengerjaan',
                'standard_code_ref' => 'KIB-F-001',
                'kib_code' => 'F',
                'sub_categories' => [
                    '01' => 'Konstruksi Gedung',
                    '02' => 'Konstruksi Jalan',
                    '03' => 'Konstruksi Lainnya'
                ]
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // 3. Buat Lokasi
        $locations = [
            // DINPORA
            [
                'name' => 'Gedung Utama DISPORA',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'opd_unit_id' => 1,
                'type' => 'gedung',
                'address' => 'Jl. Olahraga No. 1, Jakarta Pusat'
            ],
            [
                'name' => 'Lapangan Olahraga DISPORA',
                'latitude' => -6.2098,
                'longitude' => 106.8466,
                'opd_unit_id' => 1,
                'type' => 'lapangan',
                'address' => 'Jl. Olahraga No. 2, Jakarta Pusat'
            ],

            // DINPEN
            [
                'name' => 'Kantor Dinas Pendidikan',
                'latitude' => -6.2188,
                'longitude' => 106.8156,
                'opd_unit_id' => 2,
                'type' => 'gedung',
                'address' => 'Jl. Pendidikan No. 2, Jakarta Selatan'
            ],
            [
                'name' => 'Gudang Perlengkapan',
                'latitude' => -6.2198,
                'longitude' => 106.8166,
                'opd_unit_id' => 2,
                'type' => 'gudang',
                'address' => 'Jl. Pendidikan No. 3, Jakarta Selatan'
            ],

            // DINKES
            [
                'name' => 'Rumah Sakit Umum Daerah',
                'latitude' => -6.2288,
                'longitude' => 106.8256,
                'opd_unit_id' => 3,
                'type' => 'gedung',
                'address' => 'Jl. Kesehatan No. 3, Jakarta Timur'
            ],
            [
                'name' => 'Gedung Puskesmas',
                'latitude' => -6.2298,
                'longitude' => 106.8266,
                'opd_unit_id' => 3,
                'type' => 'gedung',
                'address' => 'Jl. Kesehatan No. 4, Jakarta Timur'
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }

        // 4. Buat Users
        $users = [
            // Admin Utama
            [
                'name' => 'Administrator Utama',
                'email' => 'admin@simasetdaerah.go.id',
                'password' => Hash::make('admin123'),
                'role' => 'admin_utama',
                'opd_unit_id' => null,
                'email_verified_at' => now(),
            ],

            // Admin OPD
            [
                'name' => 'Admin DISPORA',
                'email' => 'dispora@simasetdaerah.go.id',
                'password' => Hash::make('opd123'),
                'role' => 'admin_opd',
                'opd_unit_id' => 1,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin Pendidikan',
                'email' => 'pendidikan@simasetdaerah.go.id',
                'password' => Hash::make('opd123'),
                'role' => 'admin_opd',
                'opd_unit_id' => 2,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin Kesehatan',
                'email' => 'kesehatan@simasetdaerah.go.id',
                'password' => Hash::make('opd123'),
                'role' => 'admin_opd',
                'opd_unit_id' => 3,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin Pemberdayaan',
                'email' => 'pemberdayaan@simasetdaerah.go.id',
                'password' => Hash::make('opd123'),
                'role' => 'admin_opd',
                'opd_unit_id' => 4,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin Pariwisata',
                'email' => 'pariwisata@simasetdaerah.go.id',
                'password' => Hash::make('opd123'),
                'role' => 'admin_opd',
                'opd_unit_id' => 5,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin BPKAD',
                'email' => 'bpkad@simasetdaerah.go.id',
                'password' => Hash::make('opd123'),
                'role' => 'admin_opd',
                'opd_unit_id' => 6,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('📋 Login Information:');
        $this->command->info('   Admin Utama: admin@simasetdaerah.go.id / admin123');
        $this->command->info('   Admin OPD: email / opd123');
    }
}