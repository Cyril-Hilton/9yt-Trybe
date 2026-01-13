<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->enum('account_type', ['bank', 'mobile_money']);

            // Bank account details
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('branch')->nullable();
            $table->string('swift_code')->nullable();

            // Mobile money details
            $table->string('mobile_money_network')->nullable(); // MTN, Vodafone, AirtelTigo
            $table->string('mobile_money_number')->nullable();
            $table->string('mobile_money_name')->nullable();

            // Verification
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();

            // Default account
            $table->boolean('is_default')->default(false);

            $table->timestamps();

            $table->index('company_id');
            $table->index('account_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_payment_accounts');
    }
};
