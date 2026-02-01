<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news_articles', function (Blueprint $table) {
            $table->string('type')->default('news')->after('slug');
            $table->string('category')->nullable()->after('type');
            $table->string('meta_title')->nullable()->after('author');
            $table->text('meta_description')->nullable()->after('meta_title');

            $table->index(['type', 'is_published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::table('news_articles', function (Blueprint $table) {
            $table->dropIndex(['type', 'is_published', 'published_at']);
            $table->dropColumn(['type', 'category', 'meta_title', 'meta_description']);
        });
    }
};
