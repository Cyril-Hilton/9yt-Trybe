<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_inbox_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique();
            $table->string('from_email')->index();
            $table->string('from_name')->nullable();
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->string('classification')->default('general')->index();
            $table->string('status')->default('received')->index();
            $table->text('reply_body')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_inbox_messages');
    }
};
