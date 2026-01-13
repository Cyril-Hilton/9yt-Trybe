<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('payout_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_account_id')->constrained('organization_payment_accounts')->onDelete('cascade');

            // Amount details
            $table->decimal('gross_amount', 15, 2); // total ticket sales
            $table->decimal('platform_fees', 15, 2); // fees deducted
            $table->decimal('net_amount', 15, 2); // amount to be paid out
            $table->string('currency')->default('GHS');

            // Payout status
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('failure_reason')->nullable();

            // Payout details
            $table->string('payout_method'); // bank_transfer, mobile_money
            $table->text('payout_reference')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('company_id');
            $table->index('event_id');
            $table->index('status');
            $table->index('payout_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_payouts');
    }
};
