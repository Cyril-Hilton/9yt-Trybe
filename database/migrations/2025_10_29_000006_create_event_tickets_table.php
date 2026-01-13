<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_section_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['paid', 'free', 'donation']);
            $table->decimal('price', 10, 2)->default(0); // in GHS
            $table->decimal('min_donation', 10, 2)->nullable(); // for donation tickets

            // Quantity settings
            $table->unsignedInteger('quantity')->nullable(); // null = unlimited
            $table->unsignedInteger('sold')->default(0);
            $table->unsignedInteger('min_per_order')->default(1);
            $table->unsignedInteger('max_per_order')->default(10);

            // Sale dates
            $table->dateTime('sales_start')->nullable();
            $table->dateTime('sales_end')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_hidden')->default(false); // hidden tickets (invitation-only)

            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('event_id');
            $table->index('event_section_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_tickets');
    }
};
