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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('contestant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Null if guest voting allowed

            // Voter information (for guest voters or backup)
            $table->string('voter_name')->nullable();
            $table->string('voter_email')->nullable();
            $table->string('voter_phone')->nullable();
            $table->string('voter_ip')->nullable();
            $table->string('voter_country')->nullable();

            // Vote details
            $table->integer('votes_count')->default(1); // How many votes in this transaction
            $table->decimal('amount_paid', 10, 2)->default(0); // Amount paid for this vote batch
            $table->enum('payment_status', ['free', 'paid', 'pending', 'failed'])->default('free');
            $table->string('payment_reference')->nullable(); // Paystack reference

            // Tracking
            $table->string('session_id')->nullable(); // For guest voters
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('poll_id');
            $table->index('contestant_id');
            $table->index('user_id');
            $table->index('voter_ip');
            $table->index('session_id');
            $table->index('payment_status');
            $table->index('created_at'); // For time-based analytics
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
