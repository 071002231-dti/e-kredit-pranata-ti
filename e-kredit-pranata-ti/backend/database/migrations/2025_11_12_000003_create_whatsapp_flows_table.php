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
        Schema::create('whatsapp_flows', function (Blueprint $table) {
            $table->id();
            $table->string('flow_id')->unique()->comment('WhatsApp Flow ID from API');
            $table->string('name')->comment('Flow name/identifier');
            $table->text('json_definition')->comment('Flow JSON structure');
            $table->enum('status', ['draft', 'published', 'deprecated'])->default('draft')->comment('Flow status');
            $table->integer('version')->default(1)->comment('Flow version number');
            $table->text('description')->nullable()->comment('Flow description');
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_flows');
    }
};
