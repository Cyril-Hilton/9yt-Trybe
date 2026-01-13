<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->longText('overview')->nullable();

            // Event type and timing
            $table->enum('event_type', ['single', 'recurring'])->default('single');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('timezone')->default('Africa/Accra');

            // Recurring event fields
            $table->enum('recurrence_pattern', ['daily', 'weekly', 'monthly', 'custom'])->nullable();
            $table->json('recurrence_config')->nullable(); // stores detailed recurrence rules
            $table->date('recurrence_end_date')->nullable();

            // Location
            $table->enum('location_type', ['venue', 'online', 'tba'])->default('venue');
            $table->string('venue_name')->nullable();
            $table->text('venue_address')->nullable();
            $table->decimal('venue_latitude', 10, 8)->nullable();
            $table->decimal('venue_longitude', 11, 8)->nullable();
            $table->string('online_platform')->nullable(); // zoom, google_meet, custom
            $table->text('online_link')->nullable();
            $table->text('online_meeting_details')->nullable();

            // Media
            $table->string('banner_image')->nullable();

            // Good to Know section
            $table->string('age_restriction')->nullable();
            $table->time('door_time')->nullable();
            $table->text('parking_info')->nullable();

            // Approval status
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'cancelled'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('admins')->nullOnDelete();

            // Fee settings (who pays the fees)
            $table->enum('fee_bearer', ['organizer', 'attendee'])->default('attendee');

            // Analytics
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('tickets_sold')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('status');
            $table->index('start_date');
            $table->index('location_type');
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
