<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if migration is already done
        $hasOwnerId = Schema::hasColumn('sms_contacts', 'owner_id');
        $hasCompanyId = Schema::hasColumn('sms_contacts', 'company_id');

        if ($hasOwnerId && !$hasCompanyId) {
            // Already migrated, skip
            return;
        }

        if (!$hasOwnerId) {
            // Step 1: Add new polymorphic columns
            Schema::table('sms_contacts', function (Blueprint $table) {
                $table->unsignedBigInteger('owner_id')->nullable()->after('id');
                $table->string('owner_type')->nullable()->after('owner_id');
            });
        }

        if ($hasCompanyId) {
            // Step 2: Migrate existing data from company_id to owner_id/owner_type
            DB::statement("
                UPDATE sms_contacts
                SET owner_id = company_id,
                    owner_type = 'App\\\\Models\\\\Company'
                WHERE company_id IS NOT NULL AND owner_id IS NULL
            ");

            // Step 3: Drop old foreign key and column
            Schema::table('sms_contacts', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        // Step 4: Make owner_id required and add index (if not already exists)
        Schema::table('sms_contacts', function (Blueprint $table) use ($hasOwnerId) {
            if (!$hasOwnerId) {
                $table->unsignedBigInteger('owner_id')->nullable(false)->change();
                $table->string('owner_type')->nullable(false)->change();
            }

            // Add composite index for polymorphic relationship if it doesn't exist
            if (!$this->indexExists('sms_contacts', 'owner_phone_index')) {
                $table->index(['owner_id', 'owner_type', 'phone_number'], 'owner_phone_index');
            }
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $schemaManager = $connection->getDoctrineSchemaManager();
        $indexes = $schemaManager->listTableIndexes($table);

        return isset($indexes[$index]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add company_id column back
        Schema::table('sms_contacts', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // Step 2: Migrate data back (only for Company owners)
        DB::statement("
            UPDATE sms_contacts
            SET company_id = owner_id
            WHERE owner_type = 'App\\\\Models\\\\Company'
        ");

        // Step 3: Drop polymorphic columns and index
        Schema::table('sms_contacts', function (Blueprint $table) {
            $table->dropIndex('owner_phone_index');
            $table->dropColumn(['owner_id', 'owner_type']);
        });
    }
};
