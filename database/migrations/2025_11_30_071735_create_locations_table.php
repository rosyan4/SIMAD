<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id('location_id');
            $table->string('name');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Koordinat latitude');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Koordinat longitude');
            
            // Foreign key ke opd_units
            $table->foreignId('opd_unit_id')->constrained('opd_units', 'opd_unit_id');
            
            // Jenis lokasi
            $table->enum('type', ['gedung', 'ruangan', 'gudang', 'lapangan', 'lainnya'])->default('gedung');
            $table->text('address')->nullable();
            $table->timestamps();
            
            // Index untuk optimasi
            $table->index('opd_unit_id');
            $table->index(['latitude', 'longitude']);
            $table->index('type');
            $table->index(['opd_unit_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};