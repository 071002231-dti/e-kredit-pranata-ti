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
        Schema::table('activities', function (Blueprint $table) {
            $table->enum('submitted_via', ['web', 'whatsapp'])->default('web')->after('status')->comment('Submission method');
            $table->string('whatsapp_message_id')->nullable()->after('submitted_via')->comment('WhatsApp message ID if submitted via WhatsApp');

            // Index
            $table->index('submitted_via');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['submitted_via', 'whatsapp_message_id']);
        });
    }
};
