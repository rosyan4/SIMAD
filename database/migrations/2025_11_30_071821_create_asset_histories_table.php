<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_histories', function (Blueprint $table) {
            $table->id('history_id');
            $table->foreignId('asset_id')->constrained('assets', 'asset_id')->onDelete('cascade');
            
            // Aksi yang dilakukan
            $table->enum('action', ['create', 'update', 'mutasi', 'hapus', 'restore', 'verifikasi', 'validasi', 'pemeliharaan', 'penyusutan']);
            $table->text('description');
            
            // Data sebelum dan sesudah perubahan (JSON)
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            
            // Informasi perubahan
            $table->timestamp('change_date')->useCurrent();
            $table->foreignId('change_by')->nullable()->constrained('users', 'user_id')->comment('NULL untuk perubahan sistem');
            
            // IP address untuk audit
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            // Index untuk optimasi query dan reporting
            $table->index('asset_id');
            $table->index('action');
            $table->index('change_date');
            $table->index('change_by');
            $table->index(['asset_id', 'change_date']);
            $table->index(['change_by', 'change_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_histories');
    }
};