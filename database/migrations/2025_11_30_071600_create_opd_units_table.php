<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opd_units', function (Blueprint $table) {
            $table->id('opd_unit_id');
            $table->string('kode_opd', 10)->unique();
            $table->integer('kode_opd_numeric')->nullable()->after('kode_opd')->unique();
            $table->string('nama_opd');
            $table->string('alamat')->nullable();
            $table->string('kepala_opd')->nullable();
            $table->string('nip_kepala_opd', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk optimasi
            $table->index('kode_opd_numeric');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opd_units');
    }
};