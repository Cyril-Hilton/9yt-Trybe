<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');

            // Attendee info
            $table->string('attendee_name');
            $table->string('attendee_email');

            // Ticket info
            $table->string('ticket_code', 6)->unique(); // 6-digit code
            $table->string('qr_code_path')->nullable(); // path to QR code image
            $table->decimal('price_paid', 10, 2);

            // Check-in
            $table->boolean('checked_in')->default(false);
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();

            // Status
            $table->enum('status', ['valid', 'cancelled', 'refunded'])->default('valid');

            $table->timestamps();

            $table->index('event_order_id');
            $table->index('event_ticket_id');
            $table->index('event_id');
            $table->index('ticket_code');
            $table->index('checked_in');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendees');
    }
};
