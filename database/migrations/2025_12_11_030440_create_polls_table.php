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
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained()->onDelete('cascade'); // Optional: Link to event
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('banner_image')->nullable();

            // Poll settings
            $table->enum('poll_type', ['pageant', 'contest', 'election', 'voting', 'survey', 'general'])->default('general');
            $table->enum('voting_type', ['free', 'paid'])->default('free');
            $table->decimal('vote_price', 10, 2)->default(0); // Price per vote if paid
            $table->integer('votes_per_transaction')->default(1); // How many votes user can buy at once
            $table->boolean('allow_multiple_votes')->default(true); // Can user vote multiple times?
            $table->integer('max_votes_per_user')->nullable(); // Null = unlimited

            // Visibility & Status
            $table->enum('status', ['draft', 'active', 'closed', 'archived'])->default('draft');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('show_results')->default(true); // Show real-time results to public?
            $table->boolean('require_login')->default(false); // Must be logged in to vote?

            // Analytics
            $table->unsignedBigInteger('total_votes')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0); // For paid votes
            $table->unsignedBigInteger('unique_voters')->default(0);
            $table->unsignedBigInteger('views_count')->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('event_id');
            $table->index('status');
            $table->index('poll_type');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
