<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class HolidayService
{
    /**
     * Get holidays for a specific country and year using Nager.Date API (free, no API key needed)
     *
     * @param string $countryCode ISO 3166-1 alpha-2 country code (e.g., 'GH', 'US', 'GB')
     * @param int|null $year
     * @return array
     */
    public function getHolidays(string $countryCode, ?int $year = null): array
    {
        $year = $year ?? now()->year;
        $cacheKey = "holidays_{$countryCode}_{$year}";

        // Cache holidays for 24 hours
        return Cache::remember($cacheKey, 60 * 60 * 24, function () use ($countryCode, $year) {
            try {
                $response = Http::timeout(10)
                    ->get("https://date.nager.at/api/v3/PublicHolidays/{$year}/{$countryCode}");

                if ($response->successful()) {
                    return $response->json();
                }

                return [];
            } catch (\Exception $e) {
                \Log::error('Holiday API Error: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get holiday for a specific date
     *
     * @param string $countryCode
     * @param string $date Format: YYYY-MM-DD
     * @return array|null
     */
    public function getHolidayForDate(string $countryCode, string $date): ?array
    {
        $carbon = Carbon::parse($date);
        $holidays = $this->getHolidays($countryCode, $carbon->year);

        foreach ($holidays as $holiday) {
            if ($holiday['date'] === $carbon->format('Y-m-d')) {
                return $holiday;
            }
        }

        return null;
    }

    /**
     * Get upcoming holidays (next 30 days)
     *
     * @param string $countryCode
     * @return array
     */
    public function getUpcomingHolidays(string $countryCode, int $days = 30): array
    {
        $holidays = $this->getHolidays($countryCode);
        $today = now();
        $upcoming = [];

        foreach ($holidays as $holiday) {
            $holidayDate = Carbon::parse($holiday['date']);

            if ($holidayDate->isFuture() && $holidayDate->diffInDays($today) <= $days) {
                $upcoming[] = $holiday;
            }
        }

        return $upcoming;
    }

    /**
     * Get all available country codes
     *
     * @return array
     */
    public function getAvailableCountries(): array
    {
        $cacheKey = 'holiday_countries';

        return Cache::remember($cacheKey, 60 * 60 * 24 * 7, function () {
            try {
                $response = Http::timeout(10)
                    ->get('https://date.nager.at/api/v3/AvailableCountries');

                if ($response->successful()) {
                    return $response->json();
                }

                // Fallback to common countries
                return $this->getCommonCountries();
            } catch (\Exception $e) {
                \Log::error('Holiday Countries API Error: ' . $e->getMessage());
                return $this->getCommonCountries();
            }
        });
    }

    /**
     * Get common countries as fallback
     *
     * @return array
     */
    private function getCommonCountries(): array
    {
        return [
            ['countryCode' => 'GH', 'name' => 'Ghana'],
            ['countryCode' => 'NG', 'name' => 'Nigeria'],
            ['countryCode' => 'US', 'name' => 'United States'],
            ['countryCode' => 'GB', 'name' => 'United Kingdom'],
            ['countryCode' => 'CA', 'name' => 'Canada'],
            ['countryCode' => 'AU', 'name' => 'Australia'],
            ['countryCode' => 'ZA', 'name' => 'South Africa'],
            ['countryCode' => 'KE', 'name' => 'Kenya'],
            ['countryCode' => 'IN', 'name' => 'India'],
            ['countryCode' => 'FR', 'name' => 'France'],
            ['countryCode' => 'DE', 'name' => 'Germany'],
            ['countryCode' => 'IT', 'name' => 'Italy'],
            ['countryCode' => 'ES', 'name' => 'Spain'],
            ['countryCode' => 'BR', 'name' => 'Brazil'],
            ['countryCode' => 'MX', 'name' => 'Mexico'],
            ['countryCode' => 'JP', 'name' => 'Japan'],
            ['countryCode' => 'CN', 'name' => 'China'],
            ['countryCode' => 'SG', 'name' => 'Singapore'],
            ['countryCode' => 'AE', 'name' => 'United Arab Emirates'],
        ];
    }

    /**
     * Check if a date is a public holiday
     *
     * @param string $countryCode
     * @param string $date
     * @return bool
     */
    public function isPublicHoliday(string $countryCode, string $date): bool
    {
        return $this->getHolidayForDate($countryCode, $date) !== null;
    }

    /**
     * Get country code from IP address (using ipapi.co - free tier)
     *
     * @param string|null $ipAddress
     * @return string|null
     */
    public function getCountryFromIP(?string $ipAddress = null): ?string
    {
        $ipAddress = $ipAddress ?? request()->ip();

        // Don't try to geolocate local IPs
        if (in_array($ipAddress, ['127.0.0.1', '::1', 'localhost'])) {
            return 'GH'; // Default to Ghana for local development
        }

        $cacheKey = "country_ip_{$ipAddress}";

        return Cache::remember($cacheKey, 60 * 60 * 24, function () use ($ipAddress) {
            try {
                $response = Http::timeout(5)
                    ->get("https://ipapi.co/{$ipAddress}/country/");

                if ($response->successful()) {
                    $countryCode = trim($response->body());
                    if (strlen($countryCode) === 2) {
                        return strtoupper($countryCode);
                    }
                }

                return 'GH'; // Default fallback
            } catch (\Exception $e) {
                \Log::error('IP Geolocation Error: ' . $e->getMessage());
                return 'GH'; // Default fallback
            }
        });
    }
}
