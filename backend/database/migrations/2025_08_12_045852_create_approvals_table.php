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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade')->comment('ID aktivitas');
            $table->foreignId('verifier_id')->constrained('users')->onDelete('restrict')->comment('ID verifikator');
            $table->enum('status', ['approved', 'rejected'])->index()->comment('Status approval');
            $table->text('comments')->nullable()->comment('Catatan verifikator');
            $table->timestamp('approved_at')->useCurrent()->comment('Waktu approve/reject');
            $table->timestamps();

            // Indexes for common queries
            $table->index('activity_id');
            $table->index('verifier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
