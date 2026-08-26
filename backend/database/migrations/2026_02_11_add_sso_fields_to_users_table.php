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
        Schema::table('users', function (Blueprint $table) {
            // Make existing fields nullable for SSO-only users
            $table->string('nip', 18)->nullable()->change();
            $table->string('password')->nullable()->change();

            // Add SSO fields
            $table->string('nip_sso', 9)->nullable()->unique()->after('nip')
                ->comment('9-digit SSO identifier (without @uii.ac.id)');
            $table->string('sso_uid', 50)->nullable()->unique()->after('nip_sso')
                ->comment('IdP unique identifier (uid from SAML)');
            $table->enum('auth_method', ['local', 'sso', 'hybrid'])
                ->default('local')->after('password')
                ->comment('Authentication method used by this user');
            $table->timestamp('sso_last_login')->nullable()->after('auth_method')
                ->comment('Last successful SSO login timestamp');
            $table->string('affiliation', 100)->nullable()->after('unit_kerja')
                ->comment('eduPersonAffiliation from IdP (faculty, staff, student)');

            // Indexes for performance
            $table->index('nip_sso', 'users_nip_sso_index');
            $table->index('sso_uid', 'users_sso_uid_index');
            $table->index('auth_method', 'users_auth_method_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('users_nip_sso_index');
            $table->dropIndex('users_sso_uid_index');
            $table->dropIndex('users_auth_method_index');

            // Drop SSO fields
            $table->dropColumn([
                'nip_sso',
                'sso_uid',
                'auth_method',
                'sso_last_login',
                'affiliation'
            ]);

            // Restore original constraints
            $table->string('nip', 18)->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
