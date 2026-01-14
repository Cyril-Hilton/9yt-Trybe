<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
        });

        $companies = DB::table('companies')->select('id', 'name', 'slug')->get();
        $usedSlugs = [];

        foreach ($companies as $company) {
            if (!empty($company->slug)) {
                $usedSlugs[$company->slug] = true;
                continue;
            }

            $baseSlug = Str::slug($company->name);
            if ($baseSlug === '') {
                $baseSlug = 'organizer';
            }

            $slug = $baseSlug;
            $counter = 1;

            while (isset($usedSlugs[$slug]) || DB::table('companies')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            DB::table('companies')->where('id', $company->id)->update(['slug' => $slug]);
            $usedSlugs[$slug] = true;
        }

        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'slug')) {
                $table->string('slug')->unique()->nullable(false)->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'slug')) {
                // Fix for SQLite: explicitly drop the index first
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }
};
