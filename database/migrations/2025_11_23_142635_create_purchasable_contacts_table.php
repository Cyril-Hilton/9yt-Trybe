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
        Schema::create('purchasable_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique();
            $table->string('name')->nullable();
            $table->string('region')->nullable(); // Ghana region
            $table->enum('category', ['general', 'business', 'students', 'youth', 'professionals'])->default('general');
            $table->boolean('is_active')->default(true);
            $table->integer('times_used')->default(0); // Track how many times this contact has been sold
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'category']);
            $table->index('region');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchasable_contacts');
    }
};
