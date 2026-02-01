<?php

namespace App\Observers;

use App\Models\Poll;
use App\Services\SEO\IndexNowService;

class PollObserver
{
    public function created(Poll $poll): void
    {
        $this->submitIfActive($poll);
    }

    public function updated(Poll $poll): void
    {
        if ($poll->wasChanged(['status', 'slug'])) {
            $this->submitIfActive($poll);
        }
    }

    private function submitIfActive(Poll $poll): void
    {
        if ($poll->status !== 'active') {
            return;
        }

        $url = $poll->public_url;
        app(IndexNowService::class)->submitUrls([$url]);
    }
}
