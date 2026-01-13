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
            // Remove old single-category enum
            $table->dropColumn('category');

            // Add holiday tracking
            $table->boolean('is_holiday')->default(false)->after('event_type');
            $table->string('holiday_name')->nullable()->after('is_holiday');
            $table->string('holiday_country')->nullable()->after('holiday_name'); // For geolocation
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_holiday', 'holiday_name', 'holiday_country']);

            // Restore old category column
            $table->enum('category', [
                'holidays', 'music', 'nightlife', 'performing_visual_arts', 'dating',
                'hobbies', 'business', 'food_drink', 'sports_fitness', 'health_wellness',
                'community_culture', 'film_media', 'charity_causes', 'government_politics',
                'education', 'family_kids', 'fashion_beauty', 'home_lifestyle',
                'auto_boat_air', 'travel_outdoor', 'school_activities',
                'spirituality_religion', 'science_technology', 'other'
            ])->nullable();
        });
    }
};
