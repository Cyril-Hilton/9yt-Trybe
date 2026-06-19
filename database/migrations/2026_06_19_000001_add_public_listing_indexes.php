<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->index(
                ['status', 'end_date', 'start_date'],
                'events_public_listing_index'
            );
            $table->index(
                ['status', 'region', 'end_date'],
                'events_region_listing_index'
            );
        });

        Schema::table('shop_products', function (Blueprint $table) {
            $table->index(
                ['status', 'is_active', 'created_at'],
                'shop_products_public_listing_index'
            );
        });

        Schema::table('polls', function (Blueprint $table) {
            $table->index(
                ['status', 'end_date', 'start_date'],
                'polls_public_listing_index'
            );
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->index(
                ['is_suspended', 'slug'],
                'companies_public_listing_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_public_listing_index');
            $table->dropIndex('events_region_listing_index');
        });

        Schema::table('shop_products', function (Blueprint $table) {
            $table->dropIndex('shop_products_public_listing_index');
        });

        Schema::table('polls', function (Blueprint $table) {
            $table->dropIndex('polls_public_listing_index');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex('companies_public_listing_index');
        });
    }
};
