<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add subcategory_name column to store the full descriptive name
     * while keeping subcategory for the code reference (e.g., II.2)
     */
    public function up(): void
    {
        Schema::table('credit_schema', function (Blueprint $table) {
            $table->string('subcategory_name')->nullable()->after('subcategory')
                ->comment('Nama lengkap subkategori sesuai PR No. 3 Tahun 2025');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_schema', function (Blueprint $table) {
            $table->dropColumn('subcategory_name');
        });
    }
};
