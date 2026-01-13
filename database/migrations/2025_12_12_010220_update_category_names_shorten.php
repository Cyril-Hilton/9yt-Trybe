<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Shorten category names that are too long and overlapping cards
     */
    public function up(): void
    {
        // Update "Dating & Networking" to "Dating"
        DB::table('categories')
            ->where('slug', 'dating')
            ->update(['name' => 'Dating']);

        // Update "Hobbies & Special Interest" to "Hobbies"
        DB::table('categories')
            ->where('slug', 'hobbies')
            ->update(['name' => 'Hobbies']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original names
        DB::table('categories')
            ->where('slug', 'dating')
            ->update(['name' => 'Dating & Networking']);

        DB::table('categories')
            ->where('slug', 'hobbies')
            ->update(['name' => 'Hobbies & Special Interest']);
    }
};
