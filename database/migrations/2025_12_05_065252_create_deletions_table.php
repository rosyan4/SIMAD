<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_deletions', function (Blueprint $table) {
            $table->id('deletion_id');
            
            // Foreign key ke aset
            $table->foreignId('asset_id')->constrained('assets', 'asset_id');
            
            // Alasan dan detail penghapusan
            $table->enum('deletion_reason', [
                'rusak_berat',
                'kadaluarsa',
                'tidak_layak_pakai',
                'hilang',
                'dijual',
                'dihibahkan',
                'lainnya'
            ])->default('rusak_berat');
            
            $table->text('reason_details')->nullable();
            
            // Proses approval
            $table->enum('status', [
                'diusulkan',
                'diverifikasi',
                'disetujui',
                'ditolak',
                'selesai'
            ])->default('diusulkan');
            
            // User yang mengusulkan dan approve
            $table->foreignId('proposed_by')->constrained('users', 'user_id');
            $table->foreignId('verified_by')->nullable()->constrained('users', 'user_id');
            $table->foreignId('approved_by')->nullable()->constrained('users', 'user_id');
            
            // Tanggal-tanggal penting
            $table->timestamp('proposed_at')->useCurrent();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            // Dokumen pendukung
            $table->json('proposal_documents')->nullable();
            $table->json('approval_documents')->nullable();
            
            // Hasil penghapusan
            $table->enum('deletion_method', [
                'musnah',
                'jual',
                'hibah',
                'lainnya'
            ])->nullable();
            
            $table->decimal('sale_value', 15, 2)->nullable()->comment('Nilai jual jika dijual');
            $table->string('recipient')->nullable()->comment('Penerima jika dihibahkan');
            
            // Catatan
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index untuk optimasi
            $table->index('asset_id');
            $table->index('status');
            $table->index('proposed_by');
            $table->index('approved_by');
            $table->index('deletion_reason');
            $table->index(['status', 'deletion_reason']);
            $table->index(['proposed_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_deletions');
    }
};