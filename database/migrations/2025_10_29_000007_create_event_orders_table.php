<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // null for guest purchases

            // Customer info (for guests)
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();

            // Pricing
            $table->decimal('subtotal', 10, 2); // total ticket price
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('processing_fee', 10, 2)->default(0);
            $table->decimal('platform_fee', 10, 2)->default(0); // 2.8% or admin-configured
            $table->decimal('total', 10, 2); // grand total

            // Fee bearer
            $table->enum('fee_bearer', ['organizer', 'attendee'])->default('attendee');

            // Payment
            $table->enum('payment_status', ['pending', 'processing', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable(); // paystack
            $table->string('payment_reference')->nullable(); // Paystack reference
            $table->text('payment_response')->nullable(); // JSON response
            $table->timestamp('paid_at')->nullable();

            // Order status
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'refunded'])->default('pending');
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();

            $table->index('event_id');
            $table->index('user_id');
            $table->index('order_number');
            $table->index('payment_status');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_orders');
    }
};
