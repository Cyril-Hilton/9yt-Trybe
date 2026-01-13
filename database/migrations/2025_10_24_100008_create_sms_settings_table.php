<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('mnotify'); // mnotify, hubtel, etc.
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('sender_id')->nullable(); // Default sender ID
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable(); // Additional provider-specific config
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_settings');
    }
};
