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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('whatsapp_message_id')->unique()->nullable()->comment('WhatsApp message ID from API');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->comment('Associated user if authenticated');
            $table->string('from_number', 20)->comment('Sender phone number');
            $table->string('to_number', 20)->comment('Recipient phone number');
            $table->enum('direction', ['inbound', 'outbound'])->comment('Message direction');
            $table->string('type', 50)->comment('Message type: text, image, document, etc.');
            $table->text('content')->nullable()->comment('Message content/body');
            $table->json('metadata')->nullable()->comment('Additional message metadata');
            $table->enum('status', ['sent', 'delivered', 'read', 'failed', 'pending'])->default('pending')->comment('Message delivery status');
            $table->text('error_message')->nullable()->comment('Error message if failed');
            $table->timestamps();

            // Indexes for better query performance
            $table->index('user_id');
            $table->index('from_number');
            $table->index('to_number');
            $table->index('direction');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
