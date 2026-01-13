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
        // Add OAuth fields to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'oauth_provider')) {
                $table->string('oauth_provider')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'oauth_id')) {
                $table->string('oauth_id')->nullable()->after('oauth_provider');
            }
        });

        // Add OAuth fields to companies table
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'oauth_provider')) {
                $table->string('oauth_provider')->nullable()->after('password');
            }
            if (!Schema::hasColumn('companies', 'oauth_id')) {
                $table->string('oauth_id')->nullable()->after('oauth_provider');
            }
        });

        // Add OAuth fields to admins table
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'oauth_provider')) {
                $table->string('oauth_provider')->nullable()->after('password');
            }
            if (!Schema::hasColumn('admins', 'oauth_id')) {
                $table->string('oauth_id')->nullable()->after('oauth_provider');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'oauth_provider')) {
                $table->dropColumn('oauth_provider');
            }
            if (Schema::hasColumn('users', 'oauth_id')) {
                $table->dropColumn('oauth_id');
            }
        });

        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'oauth_provider')) {
                $table->dropColumn('oauth_provider');
            }
            if (Schema::hasColumn('companies', 'oauth_id')) {
                $table->dropColumn('oauth_id');
            }
        });

        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'oauth_provider')) {
                $table->dropColumn('oauth_provider');
            }
            if (Schema::hasColumn('admins', 'oauth_id')) {
                $table->dropColumn('oauth_id');
            }
        });
    }
};
