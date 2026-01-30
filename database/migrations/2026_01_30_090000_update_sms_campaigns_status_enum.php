<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function isSqlite(): bool
    {
        return Schema::getConnection()->getDriverName() === 'sqlite';
    }

    public function up(): void
    {
        if ($this->isSqlite()) {
            return;
        }

        DB::statement("ALTER TABLE sms_campaigns MODIFY status ENUM('draft','scheduled','processing','completed','failed','cancelled','pending_approval') DEFAULT 'draft'");
    }

    public function down(): void
    {
        if ($this->isSqlite()) {
            return;
        }

        DB::statement("ALTER TABLE sms_campaigns MODIFY status ENUM('draft','scheduled','processing','completed','failed','cancelled') DEFAULT 'draft'");
    }
};
