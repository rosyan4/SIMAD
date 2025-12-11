<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('standard_code_ref')->unique();
            $table->enum('kib_code', ['A', 'B', 'C', 'D', 'E', 'F'])
                  ->comment('Kode KIB: A, B, C, D, E, F');
            $table->json('sub_categories')->nullable()
                  ->comment('Sub kategori dalam format JSON');
            $table->timestamps();
            
            // Index untuk optimasi
            $table->index('kib_code');
            $table->index(['kib_code', 'standard_code_ref']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};