<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert 'status' column from ENUM to VARCHAR/STRING to avoid "Data truncated" errors
        // This is a more robust solution than constantly updating the ENUM list
        if (Schema::hasTable('sms_campaigns')) {
            Schema::table('sms_campaigns', function (Blueprint $table) {
                // We use change() to modify the existing column
                $table->string('status')->default('draft')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to ENUM if needed (though discouraged)
        if (Schema::hasTable('sms_campaigns')) {
            // For raw SQL compatibility across drivers, we might skip full revert logic 
            // or attempt a best-effort revert.
            // DB::statement("ALTER TABLE sms_campaigns MODIFY status ENUM('draft','scheduled','processing','completed','failed','cancelled','pending_approval') DEFAULT 'draft'");
        }
    }
};
