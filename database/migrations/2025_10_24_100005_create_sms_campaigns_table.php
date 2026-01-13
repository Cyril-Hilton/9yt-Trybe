<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('sender_id')->nullable(); // The actual sender ID text (e.g., "MNOTIFY")
            $table->string('name'); // Campaign name
            $table->text('message'); // SMS content
            $table->enum('type', ['single', 'bulk'])->default('bulk');
            $table->enum('status', ['draft', 'scheduled', 'processing', 'completed', 'failed', 'cancelled'])->default('draft');
            $table->integer('total_recipients')->default(0);
            $table->integer('total_sent')->default(0);
            $table->integer('total_delivered')->default(0);
            $table->integer('total_failed')->default(0);
            $table->integer('total_pending')->default(0);
            $table->integer('credits_used')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_campaigns');
    }
};
