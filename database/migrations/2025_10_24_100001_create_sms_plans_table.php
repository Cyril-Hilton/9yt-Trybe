<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Starter Plan"
            $table->text('description')->nullable();
            $table->integer('sms_credits'); // Number of SMS credits
            $table->decimal('price', 10, 2); // Price in GHS
            $table->string('badge', 50)->nullable(); // e.g., "Most Popular", "Best Value"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_plans');
    }
};
