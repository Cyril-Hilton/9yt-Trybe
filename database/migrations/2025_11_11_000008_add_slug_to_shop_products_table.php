<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\ShopProduct;

return new class extends Migration
{
    public function up()
    {
        // Drop existing slug column if it exists (from failed migration)
        try {
            Schema::table('shop_products', function (Blueprint $table) {
                $table->dropUnique(['slug']); // Drop unique constraint first if exists
            });
        } catch (\Exception $e) {
            // Unique constraint doesn't exist, continue
        }

        try {
            Schema::table('shop_products', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        } catch (\Exception $e) {
            // Column doesn't exist, continue
        }

        // Step 1: Add slug column without unique constraint
        Schema::table('shop_products', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Step 2: Populate slugs for existing products
        $products = \DB::table('shop_products')->get();
        foreach ($products as $product) {
            $slug = Str::slug($product->name);
            $count = \DB::table('shop_products')
                ->where('slug', 'LIKE', "{$slug}%")
                ->where('id', '!=', $product->id)
                ->count();

            $finalSlug = $count ? "{$slug}-{$count}" : $slug;

            \DB::table('shop_products')
                ->where('id', $product->id)
                ->update(['slug' => $finalSlug]);
        }

        // Step 3: Make slug unique and not nullable
        Schema::table('shop_products', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('shop_products', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
