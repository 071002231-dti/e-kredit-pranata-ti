<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whats_app_messages', function (Blueprint $table) {
            $table->id();
            $table->string('whatsapp_message_id', 255)->unique()->nullable();
            $table->foreignId('whatsapp_user_id')->nullable()->constrained('whats_app_users')->nullOnDelete();
            $table->string('from_number', 20)->nullable();
            $table->string('to_number', 20)->nullable();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->string('type', 50)->default('text');
            $table->text('content')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['from_number', 'created_at']);
            $table->index(['whatsapp_user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whats_app_messages');
    }
};
