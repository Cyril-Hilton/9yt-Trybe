<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Add category column with Eventbrite-style categories
            $table->enum('category', [
                'holidays',
                'music',
                'nightlife',
                'performing_visual_arts',
                'dating',
                'hobbies',
                'business',
                'food_drink',
                'sports_fitness',
                'health_wellness',
                'community_culture',
                'film_media',
                'charity_causes',
                'government_politics',
                'education',
                'family_kids',
                'fashion_beauty',
                'home_lifestyle',
                'auto_boat_air',
                'travel_outdoor',
                'school_activities',
                'spirituality_religion',
                'science_technology',
                'other'
            ])->nullable()->after('event_type');

            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
