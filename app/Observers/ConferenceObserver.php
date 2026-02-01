<?php

namespace App\Observers;

use App\Models\Conference;
use App\Services\SEO\IndexNowService;

class ConferenceObserver
{
    public function created(Conference $conference): void
    {
        $this->submitIfActive($conference);
    }

    public function updated(Conference $conference): void
    {
        if ($conference->wasChanged(['status', 'slug'])) {
            $this->submitIfActive($conference);
        }
    }

    private function submitIfActive(Conference $conference): void
    {
        if ($conference->status !== 'active') {
            return;
        }

        $url = $conference->public_url;
        app(IndexNowService::class)->submitUrls([$url]);
    }
}
