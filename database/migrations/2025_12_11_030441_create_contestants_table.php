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
        Schema::create('contestants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->string('contestant_code')->unique(); // Auto-generated code (e.g., CONT001, CONT002)
            $table->string('name');
            $table->text('bio')->nullable();
            $table->string('image')->nullable(); // Contestant photo
            $table->integer('order')->default(0); // Display order

            // Additional contestant details (flexible JSON for pageants)
            $table->json('details')->nullable(); // Age, height, measurements, hobbies, etc.

            // Analytics
            $table->unsignedBigInteger('total_votes')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0); // Revenue from this contestant's votes
            $table->unsignedBigInteger('unique_voters')->default(0);

            // Status
            $table->enum('status', ['active', 'disqualified', 'withdrawn'])->default('active');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('poll_id');
            $table->index('contestant_code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contestants');
    }
};
