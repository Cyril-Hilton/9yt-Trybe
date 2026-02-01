<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\AI\AiContentService;
use Illuminate\Http\Request;

class AiAssistController extends Controller
{
    public function generateEventCopy(Request $request, AiContentService $ai)
    {
        $payload = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'overview' => 'nullable|string|max:5000',
            'event_type' => 'nullable|string|in:single,recurring',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'location_type' => 'nullable|string|in:venue,online,tba',
            'venue_name' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'online_platform' => 'nullable|string|max:255',
            'audience' => 'nullable|string|max:255',
            'tone' => 'nullable|string|max:80',
            'key_points' => 'nullable|string|max:500',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
        ]);

        if (!empty($payload['categories'])) {
            $payload['categories'] = Category::whereIn('id', $payload['categories'])
                ->pluck('name')
                ->values()
                ->all();
        }

        $result = $ai->generateEventCopy($payload);
        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'AI is unavailable right now. Please try again shortly.',
            ], 503);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function generateSmsDraft(Request $request, AiContentService $ai)
    {
        $payload = $request->validate([
            'purpose' => 'nullable|string|max:120',
            'details' => 'required|string|max:500',
            'cta' => 'nullable|string|max:120',
            'tone' => 'nullable|string|max:80',
            'max_length' => 'nullable|integer|min:60|max:1000',
        ]);

        $result = $ai->generateSmsMessage($payload);
        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'AI is unavailable right now. Please try again shortly.',
            ], 503);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
