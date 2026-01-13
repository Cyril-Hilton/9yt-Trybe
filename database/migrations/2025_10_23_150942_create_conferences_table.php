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
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->string('venue')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            
            $table->integer('online_limit')->default(0);
            $table->integer('in_person_limit')->default(0);
            $table->integer('online_count')->default(0);
            $table->integer('in_person_count')->default(0);
            
            $table->enum('status', ['active', 'inactive', 'closed'])->default('active');
            $table->json('form_fields')->nullable();
            $table->integer('views_count')->default(0);
            
            $table->timestamps();
            
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
        Schema::dropIfExists('conferences');
    }
};
