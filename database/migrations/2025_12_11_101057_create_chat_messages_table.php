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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable(); // For guest users
            $table->string('name')->nullable(); // For guest users
            $table->string('email')->nullable(); // For guest users
            $table->text('message');
            $table->text('admin_reply')->nullable();
            $table->unsignedBigInteger('replied_by')->nullable(); // Admin user ID
            $table->timestamp('replied_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->enum('status', ['pending', 'replied', 'closed'])->default('pending');
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('company_id');
            $table->index('session_id');
            $table->index('status');
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
