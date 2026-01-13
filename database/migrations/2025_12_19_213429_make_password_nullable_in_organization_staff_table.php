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
        Schema::table('organization_staff', function (Blueprint $table) {
            // Make password nullable since we use OTP authentication
            $table->string('password')->nullable()->change();

            // Also drop unnecessary columns if they exist
            if (Schema::hasColumn('organization_staff', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('organization_staff', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_staff', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
        });
    }
};
