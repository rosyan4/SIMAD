<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depreciations', function (Blueprint $table) {
            $table->id('depreciation_id');
            
            // Foreign key ke aset
            $table->foreignId('asset_id')->constrained('assets', 'asset_id')->onDelete('cascade');
            
            // Tahun penyusutan
            $table->year('year');
            
            // Metode penyusutan
            $table->enum('method', [
                'garis_lurus',
                'saldo_menurun',
                'unit_produksi'
            ])->default('garis_lurus');
            
            // Nilai-nilai penyusutan
            $table->decimal('beginning_value', 15, 2)->comment('Nilai buku awal tahun');
            $table->decimal('depreciation_rate', 5, 2)->comment('Persentase penyusutan (%)');
            $table->decimal('depreciation_amount', 15, 2)->comment('Jumlah penyusutan tahun ini');
            $table->decimal('accumulated_depreciation', 15, 2)->comment('Akumulasi penyusutan sampai tahun ini');
            $table->decimal('ending_value', 15, 2)->comment('Nilai buku akhir tahun');
            
            // Umur ekonomis
            $table->integer('useful_life')->comment('Umur ekonomis dalam tahun');
            $table->integer('remaining_life')->comment('Sisa umur ekonomis');
            
            // Status
            $table->enum('status', ['dihitung', 'diverifikasi', 'disetujui', 'ditolak'])->default('dihitung');
            
            // User yang menghitung dan verifikasi
            $table->foreignId('calculated_by')->nullable()->constrained('users', 'user_id');
            $table->foreignId('verified_by')->nullable()->constrained('users', 'user_id');
            $table->timestamp('verified_at')->nullable();
            
            // Catatan
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index untuk optimasi
            $table->index('asset_id');
            $table->index('year');
            $table->index(['asset_id', 'year']);
            $table->index('method');
            $table->index('status');
            $table->index(['asset_id', 'status']);
            
            // Unique constraint: satu aset hanya satu entri per tahun
            $table->unique(['asset_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depreciations');
    }
};