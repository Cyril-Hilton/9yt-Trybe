<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add email tracking and event performance summary fields
     */
    public function up(): void
    {
        Schema::table('event_payouts', function (Blueprint $table) {
            // Email tracking
            $table->timestamp('congratulatory_email_sent_at')->nullable()->after('completed_at');
            $table->timestamp('payment_confirmation_email_sent_at')->nullable()->after('congratulatory_email_sent_at');

            // Event performance summary (for email)
            $table->integer('total_tickets_sold')->default(0)->after('payment_confirmation_email_sent_at');
            $table->integer('total_attendees')->default(0)->after('total_tickets_sold');
            $table->text('admin_notes')->nullable()->after('failure_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_payouts', function (Blueprint $table) {
            $table->dropColumn([
                'congratulatory_email_sent_at',
                'payment_confirmation_email_sent_at',
                'total_tickets_sold',
                'total_attendees',
                'admin_notes',
            ]);
        });
    }
};
