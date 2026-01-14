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

        $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return !empty($result);
    }

    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!$this->indexExists('events', 'events_title_index')) {
                $table->index('title');
            }
        });

        Schema::table('companies', function (Blueprint $table) {
            if (!$this->indexExists('companies', 'companies_name_index')) {
                $table->index('name');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (!$this->indexExists('categories', 'categories_name_index')) {
                $table->index('name');
            }
        });

        Schema::table('shop_products', function (Blueprint $table) {
            if (!$this->indexExists('shop_products', 'shop_products_name_index')) {
                $table->index('name');
            }
        });

        Schema::table('polls', function (Blueprint $table) {
            if (!$this->indexExists('polls', 'polls_title_index')) {
                $table->index('title');
            }
        });

        Schema::table('surveys', function (Blueprint $table) {
            if (!$this->indexExists('surveys', 'surveys_title_index')) {
                $table->index('title');
            }
        });

        Schema::table('conferences', function (Blueprint $table) {
            if (!$this->indexExists('conferences', 'conferences_title_index')) {
                $table->index('title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['title']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('shop_products', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('polls', function (Blueprint $table) {
            $table->dropIndex(['title']);
        });

        Schema::table('surveys', function (Blueprint $table) {
            $table->dropIndex(['title']);
        });

        Schema::table('conferences', function (Blueprint $table) {
            $table->dropIndex(['title']);
        });
    }
};
