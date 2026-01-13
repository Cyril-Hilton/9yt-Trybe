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
        Schema::table('sms_campaigns', function (Blueprint $table) {
            // Approval workflow fields
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->nullable()->after('status');
            $table->boolean('requires_contacts')->default(false)->after('approval_status');
            $table->integer('target_recipient_count')->nullable()->after('requires_contacts');

            // Contact purchase pricing
            $table->decimal('contact_fee_per_recipient', 10, 2)->nullable()->after('target_recipient_count');
            $table->decimal('total_contact_fee', 10, 2)->nullable()->after('contact_fee_per_recipient');
            $table->decimal('total_sms_cost', 10, 2)->nullable()->after('total_contact_fee');
            $table->decimal('total_amount', 10, 2)->nullable()->after('total_sms_cost');

            // Payment tracking
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->nullable()->after('total_amount');
            $table->string('payment_reference')->nullable()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('payment_reference');

            // Approval tracking
            $table->foreignId('approved_by')->nullable()->constrained('admins')->after('paid_at');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');

            // Add index for approval queries
            $table->index(['approval_status', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_campaigns', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropIndex(['approval_status', 'payment_status']);

            $table->dropColumn([
                'approval_status',
                'requires_contacts',
                'target_recipient_count',
                'contact_fee_per_recipient',
                'total_contact_fee',
                'total_sms_cost',
                'total_amount',
                'payment_status',
                'payment_reference',
                'paid_at',
                'approved_by',
                'approved_at',
                'rejection_reason'
            ]);
        });
    }
};
