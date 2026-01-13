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
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_external')->default(false)->after('status');
            $table->string('external_ticket_url', 500)->nullable()->after('is_external');
            $table->string('external_ussd_code', 50)->nullable()->after('external_ticket_url');
            $table->string('external_reservation_phone', 20)->nullable()->after('external_ussd_code');
            $table->text('external_description')->nullable()->after('external_reservation_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_external', 'external_ticket_url', 'external_ussd_code', 'external_reservation_phone', 'external_description']);
        });
    }
};
