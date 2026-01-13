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
 Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained()->onDelete('cascade');
            
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('attendance_type', ['online', 'in_person']);
            $table->string('unique_id', 4)->nullable()->unique();
            
            $table->boolean('attended')->default(false);
            $table->timestamp('attended_at')->nullable();
            $table->json('additional_fields')->nullable();
            
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            $table->index('conference_id');
            $table->index('email');
            $table->index('unique_id');
            $table->index('attendance_type');
            $table->index(['conference_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
