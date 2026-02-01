<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'ai_tags')) {
                $table->json('ai_tags')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('events', 'ai_faqs')) {
                $table->json('ai_faqs')->nullable()->after('ai_tags');
            }
        });

        Schema::table('polls', function (Blueprint $table) {
            if (!Schema::hasColumn('polls', 'ai_tags')) {
                $table->json('ai_tags')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('polls', 'ai_faqs')) {
                $table->json('ai_faqs')->nullable()->after('ai_tags');
            }
        });

        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('description');
            }
            if (!Schema::hasColumn('companies', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('companies', 'ai_tags')) {
                $table->json('ai_tags')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('companies', 'ai_faqs')) {
                $table->json('ai_faqs')->nullable()->after('ai_tags');
            }
        });

        Schema::table('shop_products', function (Blueprint $table) {
            if (!Schema::hasColumn('shop_products', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('description');
            }
            if (!Schema::hasColumn('shop_products', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('shop_products', 'ai_tags')) {
                $table->json('ai_tags')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('shop_products', 'ai_faqs')) {
                $table->json('ai_faqs')->nullable()->after('ai_tags');
            }
        });

        Schema::table('surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('surveys', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('description');
            }
            if (!Schema::hasColumn('surveys', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });

        Schema::table('conferences', function (Blueprint $table) {
            if (!Schema::hasColumn('conferences', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('description');
            }
            if (!Schema::hasColumn('conferences', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('description');
            }
            if (!Schema::hasColumn('categories', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'ai_faqs')) {
                $table->dropColumn('ai_faqs');
            }
            if (Schema::hasColumn('events', 'ai_tags')) {
                $table->dropColumn('ai_tags');
            }
        });

        Schema::table('polls', function (Blueprint $table) {
            if (Schema::hasColumn('polls', 'ai_faqs')) {
                $table->dropColumn('ai_faqs');
            }
            if (Schema::hasColumn('polls', 'ai_tags')) {
                $table->dropColumn('ai_tags');
            }
        });

        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'ai_faqs')) {
                $table->dropColumn('ai_faqs');
            }
            if (Schema::hasColumn('companies', 'ai_tags')) {
                $table->dropColumn('ai_tags');
            }
            if (Schema::hasColumn('companies', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('companies', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
        });

        Schema::table('shop_products', function (Blueprint $table) {
            if (Schema::hasColumn('shop_products', 'ai_faqs')) {
                $table->dropColumn('ai_faqs');
            }
            if (Schema::hasColumn('shop_products', 'ai_tags')) {
                $table->dropColumn('ai_tags');
            }
            if (Schema::hasColumn('shop_products', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('shop_products', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
        });

        Schema::table('surveys', function (Blueprint $table) {
            if (Schema::hasColumn('surveys', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('surveys', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
        });

        Schema::table('conferences', function (Blueprint $table) {
            if (Schema::hasColumn('conferences', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('conferences', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('categories', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
        });
    }
};
