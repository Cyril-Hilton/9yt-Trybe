<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('shop_order_items');
        Schema::dropIfExists('shop_orders');
        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id')->nullable();

            // Customer details
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('shipping_address');
            $table->string('city');
            $table->string('region')->nullable();

            // Order details
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Payment
            $table->enum('payment_method', ['paystack'])->default('paystack');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_reference')->nullable()->unique();
            $table->timestamp('paid_at')->nullable();

            // Order status
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('payment_status');
            $table->index('status');
        });

        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('shop_orders')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('shop_products')->onDelete('set null');

            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_order_items');
        Schema::dropIfExists('shop_orders');
    }
};
