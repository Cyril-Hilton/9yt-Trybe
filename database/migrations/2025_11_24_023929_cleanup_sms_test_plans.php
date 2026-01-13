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
        // Delete SMS plans with test/improper names
        DB::table('sms_plans')
            ->where('name', 'like', '%aves%')
            ->orWhere('name', 'like', '%gfds%')
            ->orWhere('name', 'like', '%test%')
            ->orWhere('name', 'like', '%dummy%')
            ->orWhere('name', '=', '')
            ->delete();

        // Also delete plans with very low prices that look like test data
        DB::table('sms_plans')
            ->where('price', '<', 5)
            ->where('sms_credits', '<', 50)
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to restore test data
    }
};
