<?php

namespace App\Console\Commands;

use App\Services\SEO\IndexNowService;
use Illuminate\Console\Command;

class SubmitPendingIndexNow extends Command
{
    protected $signature = 'seo:submit-indexnow';

    protected $description = 'Submit queued public URLs to IndexNow.';

    public function handle(IndexNowService $indexNow): int
    {
        $count = $indexNow->flushQueued();
        $this->info("IndexNow URLs submitted: {$count}");

        return self::SUCCESS;
    }
}
