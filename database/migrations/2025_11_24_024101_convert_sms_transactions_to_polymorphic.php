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
        Schema::table('sms_transactions', function (Blueprint $table) {
            // Add polymorphic columns if they don't exist
            if (!Schema::hasColumn('sms_transactions', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('sms_transactions', 'owner_type')) {
                $table->string('owner_type')->nullable()->after('owner_id');
            }
        });

        // Migrate existing data from company_id to polymorphic structure
        if (Schema::hasColumn('sms_transactions', 'company_id')) {
            DB::table('sms_transactions')
                ->whereNotNull('company_id')
                ->update([
                    'owner_id' => DB::raw('company_id'),
                    'owner_type' => 'App\\Models\\Company'
                ]);

            // Drop the old company_id column and its foreign key
            Schema::table('sms_transactions', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        // Make owner_id and owner_type required after migration
        Schema::table('sms_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable(false)->change();
            $table->string('owner_type')->nullable(false)->change();

            // Add index for polymorphic relationship
            $table->index(['owner_id', 'owner_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_transactions', function (Blueprint $table) {
            // Re-add company_id column
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // Migrate data back (only companies)
        DB::table('sms_transactions')
            ->where('owner_type', 'App\\Models\\Company')
            ->update(['company_id' => DB::raw('owner_id')]);

        // Drop polymorphic columns
        Schema::table('sms_transactions', function (Blueprint $table) {
            $table->dropIndex(['owner_id', 'owner_type']);
            $table->dropColumn(['owner_id', 'owner_type']);
        });
    }
};
