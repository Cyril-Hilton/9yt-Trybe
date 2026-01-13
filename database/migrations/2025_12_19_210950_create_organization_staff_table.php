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
        // Add new columns to existing table
        Schema::table('organization_staff', function (Blueprint $table) {
            if (!Schema::hasColumn('organization_staff', 'event_ids')) {
                $table->json('event_ids')->nullable()->after('company_id');
            }
            if (!Schema::hasColumn('organization_staff', 'status')) {
                $table->enum('status', ['active', 'suspended'])->default('active')->after('event_ids');
            }
            if (!Schema::hasColumn('organization_staff', 'otp_code')) {
                $table->string('otp_code')->nullable()->after('status');
            }
            if (!Schema::hasColumn('organization_staff', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            }
            if (!Schema::hasColumn('organization_staff', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('otp_expires_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_staff', function (Blueprint $table) {
            $table->dropColumn(['event_ids', 'status', 'otp_code', 'otp_expires_at', 'last_login_at']);
        });
    }
};
