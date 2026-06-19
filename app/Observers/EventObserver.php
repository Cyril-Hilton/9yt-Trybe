<?php

namespace App\Observers;

use App\Models\Event;
use App\Services\SEO\EventSeoAutomation;
use App\Services\SEO\IndexNowService;

class EventObserver
{
    public function created(Event $event): void
    {
        $this->submitIfApproved($event);
    }

    public function updated(Event $event): void
    {
        if ($event->wasChanged(['status', 'slug'])) {
            $this->submitIfApproved($event);
        }
    }

    private function submitIfApproved(Event $event): void
    {
        if ($event->status !== 'approved') {
            return;
        }

        $url = $event->public_url;
        if (!$url) {
            return;
        }

        app(EventSeoAutomation::class)->prepare($event);
        app(IndexNowService::class)->queueUrls([$url]);
    }
}
