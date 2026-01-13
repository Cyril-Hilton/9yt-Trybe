<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('phone_number');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('group')->nullable(); // e.g., "customers", "staff"
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'phone_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_contacts');
    }
};
