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
        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_response_id')->constrained()->onDelete('cascade');
            $table->foreignId('survey_question_id')->constrained()->onDelete('cascade');

            // Answer data
            $table->text('answer_text')->nullable();
            $table->json('answer_array')->nullable(); // For multiple choice questions
            $table->integer('answer_number')->nullable();
            $table->date('answer_date')->nullable();
            $table->time('answer_time')->nullable();
            $table->string('answer_file_path')->nullable();

            $table->timestamps();

            $table->index('survey_response_id');
            $table->index('survey_question_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
    }
};
