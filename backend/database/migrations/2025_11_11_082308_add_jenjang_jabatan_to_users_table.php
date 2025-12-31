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
        Schema::table('users', function (Blueprint $table) {
            $table->string('jenjang_jabatan', 100)->nullable()->after('unit_kerja')->comment('Jenjang jabatan fungsional (PTI Pelaksana, PTI Pertama, PTI Muda, etc.)');
            $table->string('golongan', 10)->nullable()->after('jenjang_jabatan')->comment('Golongan PNS (II/a, III/b, IV/c, etc.)');
            $table->decimal('target_angka_kredit', 10, 2)->nullable()->after('golongan')->comment('Target angka kredit untuk jenjang saat ini');
            $table->decimal('angka_kredit_minimal', 10, 2)->nullable()->after('target_angka_kredit')->comment('Minimal 80% dari target');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jenjang_jabatan', 'golongan', 'target_angka_kredit', 'angka_kredit_minimal']);
        });
    }
};
