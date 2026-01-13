<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HolidayController extends Controller
{
    protected $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    /**
     * Get user's country based on IP
     */
    public function detectCountry(Request $request): JsonResponse
    {
        $countryCode = $this->holidayService->getCountryFromIP();

        return response()->json([
            'countryCode' => $countryCode,
        ]);
    }

    /**
     * Get available countries
     */
    public function getCountries(): JsonResponse
    {
        $countries = $this->holidayService->getAvailableCountries();

        return response()->json([
            'countries' => $countries,
        ]);
    }

    /**
     * Get holidays for a specific date
     */
    public function checkDate(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'country' => 'required|string|size:2',
        ]);

        $holiday = $this->holidayService->getHolidayForDate(
            $request->input('country'),
            $request->input('date')
        );

        return response()->json([
            'isHoliday' => $holiday !== null,
            'holiday' => $holiday,
        ]);
    }

    /**
     * Get upcoming holidays
     */
    public function getUpcoming(Request $request): JsonResponse
    {
        $request->validate([
            'country' => 'required|string|size:2',
            'days' => 'nullable|integer|min:1|max:90',
        ]);

        $holidays = $this->holidayService->getUpcomingHolidays(
            $request->input('country'),
            $request->input('days', 30)
        );

        return response()->json([
            'holidays' => $holidays,
        ]);
    }

    /**
     * Get all holidays for a year
     */
    public function getYearHolidays(Request $request): JsonResponse
    {
        $request->validate([
            'country' => 'required|string|size:2',
            'year' => 'nullable|integer|min:2020|max:2030',
        ]);

        $holidays = $this->holidayService->getHolidays(
            $request->input('country'),
            $request->input('year')
        );

        return response()->json([
            'holidays' => $holidays,
        ]);
    }
}
