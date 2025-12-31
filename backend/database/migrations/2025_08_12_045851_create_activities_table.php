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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ID user pengaju');
            $table->foreignId('schema_id')->constrained('credit_schema')->onDelete('restrict')->comment('ID credit schema');
            $table->string('title')->comment('Judul aktivitas');
            $table->text('description')->comment('Deskripsi detail');
            $table->string('proof_file')->nullable()->comment('Path file bukti');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index()->comment('Status approval');
            $table->timestamp('submitted_at')->useCurrent()->comment('Waktu submit');
            $table->timestamps();

            // Indexes for common queries
            $table->index('user_id');
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
