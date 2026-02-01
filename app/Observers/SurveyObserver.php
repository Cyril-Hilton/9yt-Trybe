<?php

namespace App\Observers;

use App\Models\Survey;
use App\Services\SEO\IndexNowService;

class SurveyObserver
{
    public function created(Survey $survey): void
    {
        $this->submitIfActive($survey);
    }

    public function updated(Survey $survey): void
    {
        if ($survey->wasChanged(['status', 'slug'])) {
            $this->submitIfActive($survey);
        }
    }

    private function submitIfActive(Survey $survey): void
    {
        if ($survey->status !== 'active') {
            return;
        }

        $url = $survey->public_url;
        app(IndexNowService::class)->submitUrls([$url]);
    }
}
