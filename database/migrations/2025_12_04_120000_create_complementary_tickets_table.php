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
        Schema::create('complementary_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('issued_by')->constrained('admins')->onDelete('cascade');

            // Recipient details
            $table->string('recipient_name');
            $table->string('recipient_email');
            $table->string('recipient_phone')->nullable();

            // Ticket details
            $table->string('ticket_type'); // 'general', 'vip', etc.
            $table->decimal('original_price', 10, 2)->default(0); // Original ticket price
            $table->integer('quantity')->default(1);

            // Purpose and notes
            $table->string('purpose')->nullable(); // media, promoter, volunteer, influencer, student, etc.
            $table->text('notes')->nullable();

            // QR Code for validation
            $table->string('qr_code')->unique();
            $table->string('ticket_reference')->unique();

            // Status tracking
            $table->enum('status', ['active', 'used', 'cancelled'])->default('active');
            $table->timestamp('used_at')->nullable();
            $table->foreignId('scanned_by')->nullable()->constrained('admins')->onDelete('set null');

            // Visibility control for organizers
            $table->boolean('visible_to_organizer')->default(false);

            $table->timestamps();

            // Indexes
            $table->index('event_id');
            $table->index('recipient_email');
            $table->index('qr_code');
            $table->index('ticket_reference');
            $table->index('status');
        });

        // Add toggle column to events table for global visibility control
        if (!Schema::hasColumn('events', 'show_complementary_stats')) {
            Schema::table('events', function (Blueprint $table) {
                $table->boolean('show_complementary_stats')->default(false)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complementary_tickets');

        if (Schema::hasColumn('events', 'show_complementary_stats')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('show_complementary_stats');
            });
        }
    }
};
