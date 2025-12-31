<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add credit tracking fields to users table for monitoring compliance
     * and tracking progress toward target angka kredit
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Current earned credits (from approved activities)
            $table->decimal('current_credit_utama', 10, 2)->default(0)->after('angka_kredit_minimal')
                ->comment('Total kredit unsur utama yang sudah didapat');
            $table->decimal('current_credit_penunjang', 10, 2)->default(0)->after('current_credit_utama')
                ->comment('Total kredit unsur penunjang yang sudah didapat');
            $table->decimal('current_credit_total', 10, 2)->default(0)->after('current_credit_penunjang')
                ->comment('Total semua kredit (utama + penunjang)');

            // Banked credits (saved for future use)
            $table->decimal('banked_credit_utama', 10, 2)->default(0)->after('current_credit_total')
                ->comment('Kredit utama yang di-bank (belum bisa dipakai)');
            $table->decimal('banked_credit_penunjang', 10, 2)->default(0)->after('banked_credit_utama')
                ->comment('Kredit penunjang yang di-bank (belum bisa dipakai)');
            $table->decimal('banked_credit_total', 10, 2)->default(0)->after('banked_credit_penunjang')
                ->comment('Total kredit di bank');

            // Compliance percentage
            $table->decimal('compliance_percentage_utama', 5, 2)->nullable()->after('banked_credit_total')
                ->comment('Persentase unsur utama dari total (harus >= 80%)');
            $table->decimal('compliance_percentage_penunjang', 5, 2)->nullable()->after('compliance_percentage_utama')
                ->comment('Persentase unsur penunjang dari total (harus <= 20%)');

            // Progress toward target
            $table->decimal('progress_percentage', 5, 2)->default(0)->after('compliance_percentage_penunjang')
                ->comment('Persentase progress menuju target angka kredit');
            $table->boolean('is_compliant')->default(true)->after('progress_percentage')
                ->comment('Apakah memenuhi aturan 80/20');

            // Last credit update
            $table->timestamp('last_credit_updated_at')->nullable()->after('is_compliant')
                ->comment('Terakhir kali kredit diupdate (untuk cache invalidation)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'current_credit_utama',
                'current_credit_penunjang',
                'current_credit_total',
                'banked_credit_utama',
                'banked_credit_penunjang',
                'banked_credit_total',
                'compliance_percentage_utama',
                'compliance_percentage_penunjang',
                'progress_percentage',
                'is_compliant',
                'last_credit_updated_at'
            ]);
        });
    }
};
