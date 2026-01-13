<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('shop_orders', 'session_id')) {
            Schema::table('shop_orders', function (Blueprint $table) {
                $table->string('session_id')->nullable()->after('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('shop_orders', 'session_id')) {
            Schema::table('shop_orders', function (Blueprint $table) {
                $table->dropColumn('session_id');
            });
        }
    }
};
