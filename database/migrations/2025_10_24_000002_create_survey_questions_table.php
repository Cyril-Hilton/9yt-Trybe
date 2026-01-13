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
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->text('description')->nullable();

            // Question type
            $table->enum('type', [
                'short_text',
                'long_text',
                'single_choice',
                'multiple_choice',
                'dropdown',
                'linear_scale',
                'rating',
                'date',
                'time',
                'email',
                'phone',
                'number',
                'file_upload',
                'yes_no'
            ])->default('short_text');

            // Configuration
            $table->boolean('required')->default(false);
            $table->integer('order')->default(0);
            $table->json('options')->nullable(); // For choice-based questions
            $table->json('validation_rules')->nullable(); // min, max, pattern, etc.

            // Scale settings (for linear_scale and rating)
            $table->integer('scale_min')->nullable();
            $table->integer('scale_max')->nullable();
            $table->string('scale_min_label')->nullable();
            $table->string('scale_max_label')->nullable();

            // Conditional logic
            $table->json('conditional_logic')->nullable(); // Show question based on previous answers

            // File upload settings
            $table->string('allowed_file_types')->nullable(); // image, pdf, doc, etc.
            $table->integer('max_file_size')->nullable(); // in KB

            $table->timestamps();

            $table->index(['survey_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};
