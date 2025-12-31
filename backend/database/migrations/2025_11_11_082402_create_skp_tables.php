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
        // SKP (Sasaran Kerja Pegawai) - Annual performance targets
        Schema::create('skp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->year('tahun')->comment('Tahun SKP');
            $table->decimal('target_angka_kredit', 10, 2)->comment('Target angka kredit untuk tahun ini');
            $table->text('tugas_tambahan')->nullable()->comment('Tugas tambahan selain angka kredit');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'tahun'], 'unique_user_year');
            $table->index('tahun');
        });

        // SKP Items - Detailed target items per SKP
        Schema::create('skp_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skp_id')->constrained('skp')->onDelete('cascade');
            $table->foreignId('schema_id')->constrained('credit_schema')->onDelete('restrict');
            $table->integer('target_quantity')->default(1)->comment('Target jumlah kegiatan');
            $table->decimal('target_points', 10, 3)->comment('Target angka kredit untuk item ini');
            $table->integer('realisasi_quantity')->default(0)->comment('Realisasi jumlah kegiatan');
            $table->decimal('realisasi_points', 10, 3)->default(0)->comment('Realisasi angka kredit');
            $table->text('notes')->nullable()->comment('Catatan realisasi');
            $table->timestamps();

            $table->index('skp_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skp_items');
        Schema::dropIfExists('skp');
    }
};
