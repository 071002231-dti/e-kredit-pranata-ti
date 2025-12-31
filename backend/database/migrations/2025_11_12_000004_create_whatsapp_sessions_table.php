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
        Schema::create('whatsapp_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('whatsapp_phone', 20)->comment('WhatsApp phone number');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->comment('Associated user if authenticated');
            $table->string('state')->default('idle')->comment('Current conversation state');
            $table->json('context')->nullable()->comment('Conversation context data');
            $table->timestamp('last_message_at')->nullable()->comment('Last message timestamp');
            $table->timestamp('expires_at')->nullable()->comment('Session expiration');
            $table->timestamps();

            // Indexes
            $table->index('whatsapp_phone');
            $table->index('user_id');
            $table->index('state');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_sessions');
    }
};
