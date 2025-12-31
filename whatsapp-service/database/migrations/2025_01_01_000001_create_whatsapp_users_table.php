<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whats_app_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('main_app_user_id')->index();
            $table->string('whatsapp_phone', 20)->unique();
            $table->boolean('verified')->default(false);
            $table->boolean('notifications_enabled')->default(true);
            $table->timestamp('last_interaction_at')->nullable();
            $table->json('preferences')->nullable();

            // Cached user data from main app
            $table->string('cached_name', 255)->nullable();
            $table->string('cached_email', 255)->nullable();
            $table->string('cached_nip', 50)->nullable();
            $table->string('cached_jenjang_jabatan', 100)->nullable();
            $table->timestamp('cache_updated_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whats_app_users');
    }
};
