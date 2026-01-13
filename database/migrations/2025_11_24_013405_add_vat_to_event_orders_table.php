<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add VAT field for new competitive pricing model
     * - VAT is 12.5% applied on payment gateway fees only
     * - Supports transparent fee breakdown for ticket buyers
     */
    public function up(): void
    {
        Schema::table('event_orders', function (Blueprint $table) {
            // Add VAT column after processing_fee
            $table->decimal('vat', 10, 2)->default(0)->after('processing_fee');

            // Add payment_gateway_fee to be more explicit
            // (Previously used processing_fee, but this is clearer)
            $table->decimal('payment_gateway_fee', 10, 2)->default(0)->after('processing_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_orders', function (Blueprint $table) {
            $table->dropColumn(['vat', 'payment_gateway_fee']);
        });
    }
};
