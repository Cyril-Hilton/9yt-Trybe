<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
    ];

    // Get a setting value by key
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return static::castValue($setting->value, $setting->type);
        });
    }

    // Set a setting value
    public static function set(string $key, $value, string $type = 'string', string $group = 'general', string $description = null)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );

        Cache::forget("setting_{$key}");

        return $setting;
    }

    // Cast value based on type
    protected static function castValue($value, $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? (float) $value : 0,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    // Helper methods for fee settings
    public static function getPlatformFee()
    {
        return static::get('platform_fee_percentage', 2.8);
    }

    public static function getServiceFeePercentage()
    {
        return static::get('service_fee_percentage', 3.7);
    }

    public static function getServiceFeeFixed()
    {
        return static::get('service_fee_fixed', 7.16);
    }

    public static function getPaymentProcessingFee()
    {
        return static::get('payment_processing_fee', 2.9);
    }

    public static function isServiceFeeEnabled()
    {
        return static::get('service_fee_enabled', true);
    }

    // Calculate fees for an order
    public static function calculateFees($subtotal, $ticketCount = 1)
    {
        $platformFee = ($subtotal * static::getPlatformFee()) / 100;

        $serviceFee = 0;
        if (static::isServiceFeeEnabled()) {
            $serviceFee = (($subtotal * static::getServiceFeePercentage()) / 100) + (static::getServiceFeeFixed() * $ticketCount);
        }

        $totalBeforeProcessing = $subtotal + $platformFee + $serviceFee;
        $processingFee = ($totalBeforeProcessing * static::getPaymentProcessingFee()) / 100;

        return [
            'subtotal' => round($subtotal, 2),
            'platform_fee' => round($platformFee, 2),
            'service_fee' => round($serviceFee, 2),
            'processing_fee' => round($processingFee, 2),
            'total' => round($subtotal + $platformFee + $serviceFee + $processingFee, 2),
        ];
    }
}
