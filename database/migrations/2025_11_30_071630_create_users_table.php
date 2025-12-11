<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Role yang benar: hanya admin_utama dan admin_opd
            $table->enum('role', ['admin_utama', 'admin_opd'])->default('admin_opd');
            
            // Foreign key ke opd_units (nullable untuk admin_utama)
            $table->foreignId('opd_unit_id')->nullable()->constrained('opd_units', 'opd_unit_id')->onDelete('set null');
            
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            // Index untuk optimasi query
            $table->index('role');
            $table->index('opd_unit_id');
            $table->index(['role', 'opd_unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};