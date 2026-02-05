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
        if (Schema::hasTable('polls')) {
            Schema::table('polls', function (Blueprint $table) {
                // Change poll_type from ENUM to STRING to match Controller validation rules (voting, survey, etc)
                $table->string('poll_type')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('polls')) {
           // No-op or best effort revert
        }
    }
};
