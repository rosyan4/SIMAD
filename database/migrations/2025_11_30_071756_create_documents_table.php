<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id('document_id');
            $table->foreignId('asset_id')->constrained('assets', 'asset_id')->onDelete('cascade');
            $table->string('file_path')->comment('Path file di storage');
            $table->string('file_type')->comment('PDF, JPG, PNG, dll');
            $table->string('document_type')->comment('Pengadaan, Mutasi, Penghapusan, Pemeliharaan, Lainnya');
            
            // Status verifikasi
            $table->enum('verified_status', ['belum_diverifikasi', 'valid', 'tidak_valid'])
                  ->default('belum_diverifikasi')
                  ->comment('Status verifikasi dokumen');
            
            // User yang mengupload
            $table->foreignId('uploaded_by')->constrained('users', 'user_id');
            $table->timestamp('uploaded_at')->useCurrent();
            
            $table->timestamps();
            
            // Index untuk optimasi
            $table->index('asset_id');
            $table->index('uploaded_by');
            $table->index('document_type');
            $table->index('verified_status');
            $table->index(['asset_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};