<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contestants')) {
            return;
        }

        Schema::table('contestants', function (Blueprint $table) {
            if (!Schema::hasColumn('contestants', 'contestant_number')) {
                $table->string('contestant_number')->nullable()->after('contestant_code');
            }
            if (!Schema::hasColumn('contestants', 'photo')) {
                $table->string('photo')->nullable()->after('image');
            }
            if (!Schema::hasColumn('contestants', 'video_url')) {
                $table->string('video_url')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('contestants', 'social_media')) {
                $table->json('social_media')->nullable()->after('video_url');
            }
        });

        if (Schema::hasColumn('contestants', 'contestant_code') && Schema::hasColumn('contestants', 'contestant_number')) {
            DB::table('contestants')
                ->whereNull('contestant_number')
                ->whereNotNull('contestant_code')
                ->update(['contestant_number' => DB::raw('contestant_code')]);
        }

        if (Schema::hasColumn('contestants', 'image') && Schema::hasColumn('contestants', 'photo')) {
            DB::table('contestants')
                ->whereNull('photo')
                ->whereNotNull('image')
                ->update(['photo' => DB::raw('image')]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('contestants')) {
            return;
        }

        Schema::table('contestants', function (Blueprint $table) {
            if (Schema::hasColumn('contestants', 'social_media')) {
                $table->dropColumn('social_media');
            }
            if (Schema::hasColumn('contestants', 'video_url')) {
                $table->dropColumn('video_url');
            }
            if (Schema::hasColumn('contestants', 'photo')) {
                $table->dropColumn('photo');
            }
            if (Schema::hasColumn('contestants', 'contestant_number')) {
                $table->dropColumn('contestant_number');
            }
        });
    }
};
