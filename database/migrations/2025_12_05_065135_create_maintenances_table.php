<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id('maintenance_id');
            
            // Foreign key ke aset
            $table->foreignId('asset_id')->constrained('assets', 'asset_id')->onDelete('cascade');
            
            // Jenis dan detail pemeliharaan
            $table->enum('maintenance_type', [
                'rutin',
                'perbaikan',
                'kalibrasi',
                'penggantian',
                'lainnya'
            ])->default('rutin');
            
            $table->string('title');
            $table->text('description')->nullable();
            
            // Jadwal dan waktu aktual
            $table->date('scheduled_date');
            $table->date('actual_date')->nullable();
            
            // Status pemeliharaan
            $table->enum('status', [
                'dijadwalkan',
                'dalam_pengerjaan',
                'selesai',
                'ditunda',
                'dibatalkan'
            ])->default('dijadwalkan');
            
            // Biaya dan vendor
            $table->decimal('cost', 15, 2)->nullable()->default(0);
            $table->string('vendor')->nullable();
            $table->string('vendor_contact')->nullable();
            
            // Dokumen pendukung
            $table->json('supporting_documents')->nullable()->comment('Path dokumen dalam format JSON');
            
            // User yang mencatat dan approve
            $table->foreignId('recorded_by')->constrained('users', 'user_id');
            $table->foreignId('approved_by')->nullable()->constrained('users', 'user_id');
            $table->timestamp('approved_at')->nullable();
            
            // Catatan hasil
            $table->text('result_notes')->nullable();
            $table->enum('result_status', ['baik', 'perlu_perbaikan', 'rusak'])->nullable();
            
            $table->timestamps();
            
            // Index untuk optimasi
            $table->index('asset_id');
            $table->index('scheduled_date');
            $table->index('status');
            $table->index(['asset_id', 'status']);
            $table->index('maintenance_type');
            $table->index(['scheduled_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};