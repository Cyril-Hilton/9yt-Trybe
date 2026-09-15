<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('transportation_enabled')->default(false)->after('parking_info');
            $table->unsignedInteger('transport_seat_capacity')->nullable()->after('transportation_enabled');
            $table->text('transport_pickup_details')->nullable()->after('transport_seat_capacity');
        });

        Schema::table('event_attendees', function (Blueprint $table) {
            $table->boolean('transport_reserved')->default(false)->after('attendee_phone');
            $table->unsignedInteger('transport_badge_number')->nullable()->after('transport_reserved');
            $table->unsignedInteger('transport_seat_number')->nullable()->after('transport_badge_number');
            $table->index(['event_id', 'transport_reserved']);
        });
    }

    public function down(): void
    {
        Schema::table('event_attendees', function (Blueprint $table) {
            $table->dropIndex(['event_id', 'transport_reserved']);
            $table->dropColumn(['transport_reserved', 'transport_badge_number', 'transport_seat_number']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['transportation_enabled', 'transport_seat_capacity', 'transport_pickup_details']);
        });
    }
};
