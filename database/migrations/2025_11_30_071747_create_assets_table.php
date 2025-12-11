<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id('asset_id');
            $table->string('asset_code')->unique()->comment('Kode aset unik');
            $table->string('asset_code_old')->nullable()->comment('Kode aset lama sebelum migrasi'); // DITAMBAHKAN
            $table->string('name');
            $table->foreignId('category_id')->constrained('categories', 'category_id');
            $table->string('sub_category_code', 10)->nullable()->comment('Kode sub kategori');
            $table->foreignId('location_id')->nullable()->constrained('locations', 'location_id')->onDelete('set null');
            
            // Nilai dan tahun
            $table->decimal('value', 15, 2)->default(0);
            $table->year('acquisition_year');
            
            // Status aset
            $table->enum('status', ['aktif', 'dimutasi', 'dihapus', 'dalam_perbaikan', 'nonaktif'])->default('aktif');
            $table->enum('condition', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            
            // Status verifikasi dan validasi
            $table->enum('document_verification_status', ['belum_diverifikasi', 'valid', 'tidak_valid'])->default('belum_diverifikasi');
            $table->enum('validation_status', ['belum_divalidasi', 'disetujui', 'revisi', 'ditolak'])->default('belum_divalidasi');
            
            // Data KIB dalam JSON
            $table->json('kib_data')->nullable()->comment('Data KIB spesifik');
            
            // User yang membuat dan OPD pemilik
            $table->foreignId('created_by')->constrained('users', 'user_id');
            $table->foreignId('opd_unit_id')->constrained('opd_units', 'opd_unit_id');
            
            // Soft delete dan timestamp
            $table->softDeletes();
            $table->timestamps();
            
            // Index untuk optimasi query
            $table->index('asset_code');
            $table->index('status');
            $table->index('sub_category_code');
            $table->index('condition');
            $table->index(['category_id', 'acquisition_year']);
            $table->index(['location_id', 'status']);
            $table->index('document_verification_status');
            $table->index('validation_status');
            $table->index('opd_unit_id');
            $table->index(['opd_unit_id', 'status']);
            $table->index(['asset_code', 'opd_unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};