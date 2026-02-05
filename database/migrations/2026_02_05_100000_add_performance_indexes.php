<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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

        // MySQL/MariaDB
        $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return !empty($result);
    }

    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!$this->indexExists('events', 'events_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('events', 'events_start_date_index')) {
                $table->index('start_date');
            }
            if (!$this->indexExists('events', 'events_end_date_index')) {
                $table->index('end_date');
            }
            if (!$this->indexExists('events', 'events_region_index')) {
                $table->index('region');
            }
        });

        // Check if news_articles table exists first
        if (Schema::hasTable('news_articles')) {
            Schema::table('news_articles', function (Blueprint $table) {
                if (!$this->indexExists('news_articles', 'news_articles_is_published_index')) {
                    $table->index('is_published');
                }
                if (!$this->indexExists('news_articles', 'news_articles_published_at_index')) {
                    $table->index('published_at');
                }
                if (!$this->indexExists('news_articles', 'news_articles_type_index')) {
                    $table->index('type');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
            $table->dropIndex(['region']);
        });

        if (Schema::hasTable('news_articles')) {
            Schema::table('news_articles', function (Blueprint $table) {
                $table->dropIndex(['is_published']);
                $table->dropIndex(['published_at']);
                $table->dropIndex(['type']);
            });
        }
    }
};
