<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email'); // for guest followers
            $table->boolean('email_notifications')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'user_id']);
            $table->unique(['company_id', 'email']);
            $table->index('company_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_followers');
    }
};
