<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function isSqlite(): bool
    {
        return Schema::getConnection()->getDriverName() === 'sqlite';
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ($this->isSqlite()) {
            return;
        }

        // Allow global polls (no company)
        Schema::table('polls', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });
        DB::statement('ALTER TABLE polls MODIFY company_id BIGINT UNSIGNED NULL');
        Schema::table('polls', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        // Ensure poll_type supports current app values
        DB::statement("ALTER TABLE polls MODIFY poll_type ENUM('pageant','contest','election','general','voting','survey') DEFAULT 'general'");

        // Allow global surveys (no company)
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });
        DB::statement('ALTER TABLE surveys MODIFY company_id BIGINT UNSIGNED NULL');
        Schema::table('surveys', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->isSqlite()) {
            return;
        }

        Schema::table('polls', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });
        DB::statement('ALTER TABLE polls MODIFY company_id BIGINT UNSIGNED NOT NULL');
        Schema::table('polls', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        DB::statement("ALTER TABLE polls MODIFY poll_type ENUM('pageant','contest','election','general') DEFAULT 'general'");

        Schema::table('surveys', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });
        DB::statement('ALTER TABLE surveys MODIFY company_id BIGINT UNSIGNED NOT NULL');
        Schema::table('surveys', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }
};
