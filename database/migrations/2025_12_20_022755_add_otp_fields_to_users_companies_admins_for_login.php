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
        // Add OTP fields to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'otp_code')) {
                $table->string('otp_code', 6)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            }
        });

        // Add OTP fields to companies table
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'otp_code')) {
                $table->string('otp_code', 6)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('companies', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            }
        });

        // Add OTP fields to admins table
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('admins', 'otp_code')) {
                $table->string('otp_code', 6)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('admins', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['otp_code', 'otp_expires_at']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['otp_code', 'otp_expires_at']);
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['phone', 'otp_code', 'otp_expires_at']);
        });
    }
};
