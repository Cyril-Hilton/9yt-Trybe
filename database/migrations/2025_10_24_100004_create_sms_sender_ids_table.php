<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_sender_ids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('sender_id'); // Max 11 characters
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('purpose')->nullable(); // Why they need this sender ID
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_default')->default(false); // Default sender ID for company
            $table->timestamps();

            $table->unique(['company_id', 'sender_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_sender_ids');
    }
};
