<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('sms_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reference')->unique(); // Paystack reference
            $table->enum('type', ['purchase', 'manual_credit', 'bonus'])->default('purchase');
            $table->decimal('amount', 10, 2); // Amount paid
            $table->integer('credits'); // SMS credits received
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_method')->nullable(); // paystack, manual
            $table->text('meta_data')->nullable(); // JSON data
            $table->foreignId('credited_by')->nullable()->constrained('admins')->onDelete('set null'); // For manual credits
            $table->text('notes')->nullable(); // Admin notes for manual credits
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_transactions');
    }
};
