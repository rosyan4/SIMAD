<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_mutations', function (Blueprint $table) {
            $table->id('mutation_id');
            $table->foreignId('asset_id')->constrained('assets', 'asset_id')->onDelete('cascade');
            
            // Informasi OPD asal dan tujuan (foreign key)
            $table->foreignId('from_opd_unit_id')->constrained('opd_units', 'opd_unit_id');
            $table->foreignId('to_opd_unit_id')->constrained('opd_units', 'opd_unit_id');
            
            // Lokasi asal dan tujuan
            $table->foreignId('from_location_id')->nullable()->constrained('locations', 'location_id');
            $table->foreignId('to_location_id')->nullable()->constrained('locations', 'location_id');
            
            // Status mutasi
            $table->enum('status', ['diusulkan', 'disetujui', 'ditolak', 'selesai'])->default('diusulkan');
            
            // Tanggal dan user mutasi
            $table->date('mutation_date');
            $table->foreignId('mutated_by')->constrained('users', 'user_id');
            
            // Dokumen pendukung (JSON path)
            $table->json('supporting_documents')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index untuk optimasi
            $table->index('asset_id');
            $table->index('mutation_date');
            $table->index(['from_opd_unit_id', 'to_opd_unit_id']);
            $table->index('mutated_by');
            $table->index('status');
            $table->index(['asset_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_mutations');
    }
};