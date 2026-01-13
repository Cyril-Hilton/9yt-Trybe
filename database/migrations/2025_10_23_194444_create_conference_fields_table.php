<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conference_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained()->onDelete('cascade');
            $table->string('label'); // e.g., "Organization Name"
            $table->string('field_name'); // e.g., "organization_name"
            $table->enum('type', ['text', 'email', 'tel', 'textarea', 'number', 'date', 'select', 'checkbox', 'radio'])->default('text');
            $table->text('options')->nullable(); // JSON for select/radio/checkbox options
            $table->boolean('required')->default(false);
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Add custom_data column to registrations table to store custom field responses
        Schema::table('registrations', function (Blueprint $table) {
            $table->json('custom_data')->nullable()->after('attended');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('custom_data');
        });
        
        Schema::dropIfExists('conference_fields');
    }
};