<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credit_schema', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100)->index()->comment('Kategori utama');
            $table->string('subcategory')->nullable()->comment('Sub kategori');
            $table->string('activity_name')->comment('Nama aktivitas');
            $table->decimal('credit_points', 10, 2)->comment('Jumlah angka kredit');
            $table->text('description')->nullable()->comment('Deskripsi detail');
            $table->timestamps();

            // Composite index for category and subcategory
            $table->index(['category', 'subcategory']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_schema');
    }
};
