<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Converts SMS tables from company_id to polymorphic owner (owner_id, owner_type)
     * to support both User and Company ownership.
     */
    public function up(): void
    {
        // Tables to convert to polymorphic
        $tables = [
            'sms_credits',
            'sms_campaigns',
            'sms_sender_ids',
            'sms_contacts',
            'sms_messages',
            'sms_transactions'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table_blueprint) use ($table) {
                // Drop foreign key constraint if exists
                if ($this->foreignKeyExists($table, 'company_id')) {
                    $table_blueprint->dropForeign(['company_id']);
                }

                // Drop unique constraint for sms_credits
                if ($table === 'sms_credits') {
                    try {
                        DB::statement("ALTER TABLE `{$table}` DROP INDEX `sms_credits_company_id_unique`");
                    } catch (\Exception $e) {
                        // Index might not exist, continue
                    }
                }

                // Drop unique constraint for sms_sender_ids (company_id, sender_id)
                if ($table === 'sms_sender_ids') {
                    try {
                        $table_blueprint->dropUnique(['company_id', 'sender_id']);
                    } catch (\Exception $e) {
                        // Index might not exist, continue
                    }
                }
            });

            // Rename company_id to owner_id
            Schema::table($table, function (Blueprint $table_blueprint) {
                $table_blueprint->renameColumn('company_id', 'owner_id');
            });

            // Add owner_type column and set default value for existing records
            Schema::table($table, function (Blueprint $table_blueprint) {
                $table_blueprint->string('owner_type')->after('owner_id')->default('App\\Models\\Company');
            });

            // Update all existing records to have Company as owner_type
            DB::table($table)->update(['owner_type' => 'App\\Models\\Company']);

            // Add indexes for polymorphic queries
            Schema::table($table, function (Blueprint $table_blueprint) use ($table) {
                $table_blueprint->index(['owner_id', 'owner_type'], "{$table}_owner_index");

                // Re-add unique constraint for sms_sender_ids with polymorphic columns
                if ($table === 'sms_sender_ids') {
                    $table_blueprint->unique(['owner_id', 'owner_type', 'sender_id'], 'sms_sender_ids_unique');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'sms_credits',
            'sms_campaigns',
            'sms_sender_ids',
            'sms_contacts',
            'sms_messages',
            'sms_transactions'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table_blueprint) use ($table) {
                // Drop polymorphic indexes
                $table_blueprint->dropIndex("{$table}_owner_index");

                // Drop unique constraint for sms_sender_ids
                if ($table === 'sms_sender_ids') {
                    $table_blueprint->dropUnique('sms_sender_ids_unique');
                }
            });

            // Drop owner_type column
            Schema::table($table, function (Blueprint $table_blueprint) {
                $table_blueprint->dropColumn('owner_type');
            });

            // Rename owner_id back to company_id
            Schema::table($table, function (Blueprint $table_blueprint) {
                $table_blueprint->renameColumn('owner_id', 'company_id');
            });

            // Re-add foreign key and constraints
            Schema::table($table, function (Blueprint $table_blueprint) use ($table) {
                $table_blueprint->foreign('company_id')
                    ->references('id')
                    ->on('companies')
                    ->onDelete('cascade');

                // Re-add unique constraint for sms_credits
                if ($table === 'sms_credits') {
                    $table_blueprint->unique('company_id');
                }

                // Re-add unique constraint for sms_sender_ids
                if ($table === 'sms_sender_ids') {
                    $table_blueprint->unique(['company_id', 'sender_id']);
                }
            });
        }
    }

    /**
     * Check if foreign key exists on a table
     */
    private function foreignKeyExists(string $table, string $column): bool
    {
        $foreignKeys = DB::select(
            "SELECT CONSTRAINT_NAME
             FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = ?
             AND COLUMN_NAME = ?
             AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$table, $column]
        );

        return count($foreignKeys) > 0;
    }
};
