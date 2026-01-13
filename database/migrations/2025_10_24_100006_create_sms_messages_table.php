<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('sms_campaign_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('recipient'); // Phone number
            $table->text('message');
            $table->string('sender_id')->nullable();
            $table->enum('status', ['pending', 'submitted', 'delivered', 'failed', 'rejected', 'expired'])->default('pending');
            $table->string('external_id')->nullable(); // API message ID
            $table->integer('credits_used')->default(1);
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('api_response')->nullable(); // Store API response
            $table->timestamps();

            $table->index('status');
            $table->index('recipient');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
