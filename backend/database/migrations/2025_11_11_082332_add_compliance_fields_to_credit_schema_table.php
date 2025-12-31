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
        Schema::table('credit_schema', function (Blueprint $table) {
            // Change credit_points to support 3 decimal places (0.147, 2.319)
            $table->decimal('credit_points', 10, 3)->change();

            // Add new compliance fields from PR No. 3 Tahun 2025
            $table->string('satuan_hasil', 50)->nullable()->after('credit_points')->comment('Satuan hasil: Kali, Program, Dokumen, Sertifikat, Ijazah, dll');
            $table->string('batasan_penilaian', 255)->nullable()->after('satuan_hasil')->comment('Batasan: "25 program/tahun", "12 kali/tahun", "tidak terbatas"');
            $table->string('pelaksana', 100)->nullable()->after('batasan_penilaian')->comment('PTI Pertama, PTI Muda, PTI Madya, PTI Utama, atau "Semua Jenjang"');
            $table->string('bukti_fisik', 255)->nullable()->after('pelaksana')->comment('Jenis bukti fisik yang diperlukan');
            $table->enum('unsur_type', ['utama', 'penunjang'])->default('utama')->after('bukti_fisik')->comment('Unsur utama harus ≥80%, penunjang ≤20%');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_schema', function (Blueprint $table) {
            $table->dropColumn(['satuan_hasil', 'batasan_penilaian', 'pelaksana', 'bukti_fisik', 'unsur_type']);
            $table->decimal('credit_points', 10, 2)->change();
        });
    }
};
