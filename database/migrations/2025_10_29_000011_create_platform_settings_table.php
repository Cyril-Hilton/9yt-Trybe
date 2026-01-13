<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, number, boolean, json
            $table->text('description')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();

            $table->index('key');
            $table->index('group');
        });

        // Insert default fee settings
        DB::table('platform_settings')->insert([
            [
                'key' => 'platform_fee_percentage',
                'value' => '2.8',
                'type' => 'number',
                'description' => 'Platform fee percentage charged on ticket sales',
                'group' => 'fees',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'service_fee_percentage',
                'value' => '3.7',
                'type' => 'number',
                'description' => 'Service fee percentage to keep the platform running',
                'group' => 'fees',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'service_fee_fixed',
                'value' => '7.16',
                'type' => 'number',
                'description' => 'Fixed service fee per ticket in GHS (equivalent to $1.79)',
                'group' => 'fees',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'payment_processing_fee',
                'value' => '2.9',
                'type' => 'number',
                'description' => 'Payment processing fee percentage per order',
                'group' => 'fees',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'service_fee_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable or disable service fee charges',
                'group' => 'fees',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
