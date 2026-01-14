<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $indexName): bool
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $quotedTable = str_replace("'", "''", $table);
            $indexes = DB::select("PRAGMA index_list('{$quotedTable}')");
            foreach ($indexes as $index) {
                if ((property_exists($index, 'name') && $index->name === $indexName)
                    || (is_array($index) && ($index['name'] ?? null) === $indexName)) {
                    return true;
                }
            }

            return false;
        }

        $result = DB::select(
            'SELECT COUNT(*) as count FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$table, $indexName]
        );

        return isset($result[0]) && (int) $result[0]->count > 0;
    }

    public function up(): void
    {
        if (!Schema::hasTable('sms_credits')) {
            return;
        }

        if ($this->indexExists('sms_credits', 'sms_credits_company_id_unique')) {
            Schema::table('sms_credits', function (Blueprint $table) {
                $table->dropUnique('sms_credits_company_id_unique');
            });
        }

        if (Schema::hasColumn('sms_credits', 'owner_id') && Schema::hasColumn('sms_credits', 'owner_type')) {
            if (!$this->indexExists('sms_credits', 'sms_credits_owner_unique')) {
                Schema::table('sms_credits', function (Blueprint $table) {
                    $table->unique(['owner_id', 'owner_type'], 'sms_credits_owner_unique');
                });
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('sms_credits')) {
            return;
        }

        if ($this->indexExists('sms_credits', 'sms_credits_owner_unique')) {
            Schema::table('sms_credits', function (Blueprint $table) {
                $table->dropUnique('sms_credits_owner_unique');
            });
        }

        if (Schema::hasColumn('sms_credits', 'company_id')) {
            if (!$this->indexExists('sms_credits', 'sms_credits_company_id_unique')) {
                Schema::table('sms_credits', function (Blueprint $table) {
                    $table->unique('company_id', 'sms_credits_company_id_unique');
                });
            }
        }
    }
};
