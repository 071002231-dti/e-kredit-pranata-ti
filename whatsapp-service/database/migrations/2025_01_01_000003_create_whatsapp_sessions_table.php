<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whats_app_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('whatsapp_phone', 20)->index();
            $table->foreignId('whatsapp_user_id')->nullable()->constrained('whats_app_users')->nullOnDelete();
            $table->string('state', 100)->nullable();
            $table->json('context')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whats_app_sessions');
    }
};
