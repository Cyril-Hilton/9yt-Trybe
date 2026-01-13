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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();

            // Survey settings
            $table->enum('status', ['draft', 'active', 'paused', 'closed'])->default('draft');
            $table->boolean('allow_anonymous')->default(true);
            $table->boolean('allow_multiple_responses')->default(false);
            $table->boolean('show_progress_bar')->default(true);
            $table->boolean('randomize_questions')->default(false);
            $table->integer('response_limit')->default(0); // 0 = unlimited

            // Scheduling
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();

            // Customization
            $table->string('theme_color')->default('#3b82f6');
            $table->string('button_text')->default('Submit');
            $table->text('thank_you_message')->nullable();
            $table->string('redirect_url')->nullable();

            // Analytics
            $table->integer('views_count')->default(0);
            $table->integer('responses_count')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0); // percentage
            $table->integer('average_time_seconds')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('status');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
