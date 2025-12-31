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
        Schema::create('whatsapp_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade')->comment('Link to users table');
            $table->string('whatsapp_phone', 20)->unique()->comment('WhatsApp phone number with country code');
            $table->boolean('verified')->default(false)->comment('Whether the phone number is verified');
            $table->boolean('notifications_enabled')->default(true)->comment('User preference for WhatsApp notifications');
            $table->timestamp('last_interaction_at')->nullable()->comment('Last time user interacted via WhatsApp');
            $table->json('preferences')->nullable()->comment('User WhatsApp preferences');
            $table->timestamps();

            // Indexes
            $table->index('whatsapp_phone');
            $table->index('verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_users');
    }
};
