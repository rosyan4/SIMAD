<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_reports', function (Blueprint $table) {
            $table->id('audit_id');
            $table->foreignId('asset_id')->constrained('assets', 'asset_id')->onDelete('cascade');
            
            // Auditor adalah admin_utama
            $table->foreignId('auditor_id')->constrained('users', 'user_id')->comment('Admin utama sebagai auditor');
            
            // Temuan audit
            $table->text('findings')->nullable();
            $table->date('audit_date');
            
            // Status audit
            $table->enum('status', ['dalam_proses', 'selesai', 'perlu_tindak_lanjut'])->default('dalam_proses');
            
            // File laporan jika ada
            $table->string('report_file_path')->nullable();
            
            // Tindak lanjut
            $table->text('follow_up')->nullable();
            $table->date('follow_up_deadline')->nullable();
            
            $table->timestamps();
            
            // Index untuk optimasi
            $table->index('asset_id');
            $table->index('auditor_id');
            $table->index('audit_date');
            $table->index('status');
            $table->index(['asset_id', 'audit_date']);
            $table->index(['status', 'follow_up_deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_reports');
    }
};