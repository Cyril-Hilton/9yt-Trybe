<?php

namespace App\Console\Commands;

use App\Services\SupportInboxService;
use Illuminate\Console\Command;

class ProcessSupportInbox extends Command
{
    protected $signature = 'support:process-inbox';
    protected $description = 'Process new support mailbox messages and send safe automatic replies.';

    public function handle(SupportInboxService $inbox): int
    {
        $result = $inbox->process();
        $this->info("Processed {$result['processed']}; replied {$result['replied']}; escalated {$result['escalated']}.");
        return self::SUCCESS;
    }
}
