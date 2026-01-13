<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if a foreign key exists on a table
     */
    private function foreignKeyExists($table, $column)
    {
        $databaseName = DB::getDatabaseName();
        $result = DB::select(
            "SELECT COUNT(*) as count FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
             AND TABLE_NAME = ?
             AND COLUMN_NAME = ?
             AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$databaseName, $table, $column]
        );

        return $result[0]->count > 0;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert sms_credits table
        if (!Schema::hasColumn('sms_credits', 'owner_type')) {
            Schema::table('sms_credits', function (Blueprint $table) {
                // Add polymorphic columns
                $table->morphs('owner');
            });
        }

        // Migrate existing data if company_id column still exists
        if (Schema::hasColumn('sms_credits', 'company_id')) {
            DB::statement("UPDATE sms_credits SET owner_type = 'App\\\\Models\\\\Company', owner_id = company_id WHERE company_id IS NOT NULL AND (owner_type IS NULL OR owner_type = '')");

            // Drop foreign key if it exists
            if ($this->foreignKeyExists('sms_credits', 'company_id')) {
                Schema::table('sms_credits', function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                });
            }

            // Drop unique constraint
            $indexExists = DB::select(
                "SELECT COUNT(*) as count FROM information_schema.STATISTICS
                 WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME = 'sms_credits'
                 AND INDEX_NAME = 'sms_credits_company_id_unique'"
            );

            if ($indexExists[0]->count > 0) {
                Schema::table('sms_credits', function (Blueprint $table) {
                    $table->dropUnique(['company_id']);
                });
            }

            // Drop the company_id column
            Schema::table('sms_credits', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        // Convert sms_campaigns table
        if (Schema::hasColumn('sms_campaigns', 'company_id')) {
            if (!Schema::hasColumn('sms_campaigns', 'owner_type')) {
                Schema::table('sms_campaigns', function (Blueprint $table) {
                    $table->morphs('owner');
                });
            }

            DB::statement("UPDATE sms_campaigns SET owner_type = 'App\\\\Models\\\\Company', owner_id = company_id WHERE company_id IS NOT NULL AND (owner_type IS NULL OR owner_type = '')");

            if ($this->foreignKeyExists('sms_campaigns', 'company_id')) {
                Schema::table('sms_campaigns', function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                });
            }

            Schema::table('sms_campaigns', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        // Convert sms_contacts table
        if (Schema::hasColumn('sms_contacts', 'company_id')) {
            if (!Schema::hasColumn('sms_contacts', 'owner_type')) {
                Schema::table('sms_contacts', function (Blueprint $table) {
                    $table->morphs('owner');
                });
            }

            DB::statement("UPDATE sms_contacts SET owner_type = 'App\\\\Models\\\\Company', owner_id = company_id WHERE company_id IS NOT NULL AND (owner_type IS NULL OR owner_type = '')");

            if ($this->foreignKeyExists('sms_contacts', 'company_id')) {
                Schema::table('sms_contacts', function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                });
            }

            Schema::table('sms_contacts', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        // Convert sms_sender_ids table
        if (Schema::hasColumn('sms_sender_ids', 'company_id')) {
            if (!Schema::hasColumn('sms_sender_ids', 'owner_type')) {
                Schema::table('sms_sender_ids', function (Blueprint $table) {
                    $table->morphs('owner');
                });
            }

            DB::statement("UPDATE sms_sender_ids SET owner_type = 'App\\\\Models\\\\Company', owner_id = company_id WHERE company_id IS NOT NULL AND (owner_type IS NULL OR owner_type = '')");

            if ($this->foreignKeyExists('sms_sender_ids', 'company_id')) {
                Schema::table('sms_sender_ids', function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                });
            }

            Schema::table('sms_sender_ids', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        // Convert sms_transactions table
        if (Schema::hasColumn('sms_transactions', 'company_id')) {
            if (!Schema::hasColumn('sms_transactions', 'owner_type')) {
                Schema::table('sms_transactions', function (Blueprint $table) {
                    $table->morphs('owner');
                });
            }

            DB::statement("UPDATE sms_transactions SET owner_type = 'App\\\\Models\\\\Company', owner_id = company_id WHERE company_id IS NOT NULL AND (owner_type IS NULL OR owner_type = '')");

            if ($this->foreignKeyExists('sms_transactions', 'company_id')) {
                Schema::table('sms_transactions', function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                });
            }

            Schema::table('sms_transactions', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse for sms_credits
        Schema::table('sms_credits', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
        });

        DB::statement("UPDATE sms_credits SET company_id = owner_id WHERE owner_type = 'App\\\\Models\\\\Company'");

        Schema::table('sms_credits', function (Blueprint $table) {
            $table->dropMorphs('owner');
            $table->unique('company_id');
        });
    }
};
