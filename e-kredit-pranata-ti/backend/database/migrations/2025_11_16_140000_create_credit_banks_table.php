<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Credit Banking System: Menyimpan kredit yang belum bisa digunakan
     * karena belum sesuai dengan jenjang jabatan user saat ini.
     * Kredit ini akan otomatis ter-unlock saat user naik jabatan.
     */
    public function up(): void
    {
        Schema::create('credit_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->foreignId('credit_schema_id')->nullable()->constrained('credit_schema')->onDelete('set null');

            // Credit details
            $table->decimal('credit_points', 10, 2)->comment('Jumlah kredit yang disimpan');
            $table->string('unsur_type', 20)->comment('utama atau penunjang');
            $table->string('activity_category')->comment('Kategori aktivitas');

            // Banking status
            $table->enum('status', ['banked', 'unlocked', 'used'])->default('banked')
                ->comment('banked: masih di tabungan, unlocked: bisa dipakai, used: sudah dipakai');

            // Jabatan requirements
            $table->string('required_jenjang')->comment('Jenjang minimal untuk unlock kredit ini');
            $table->string('required_golongan')->nullable()->comment('Golongan minimal untuk unlock kredit ini');
            $table->string('current_jenjang_when_banked')->comment('Jenjang user saat kredit di-bank');

            // Unlock tracking
            $table->timestamp('banked_at')->nullable()->comment('Kapan kredit di-bank');
            $table->timestamp('unlocked_at')->nullable()->comment('Kapan kredit ter-unlock');
            $table->timestamp('used_at')->nullable()->comment('Kapan kredit digunakan');

            // Notes
            $table->text('reason')->nullable()->comment('Alasan kenapa di-bank (misal: belum cukup jenjang)');
            $table->text('unlock_notes')->nullable()->comment('Catatan saat unlock');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'required_jenjang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_banks');
    }
};
