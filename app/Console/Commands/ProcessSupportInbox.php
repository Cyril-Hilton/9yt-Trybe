<?php

namespace App\Console\Commands;

use App\Services\SupportInboxService;
use Illuminate\Console\Command;

class ProcessSupportInbox extends Command
{
    protected $signature = 'support:process-inbox
                            {--bootstrap : Record currently unread messages without replying to them}';
    protected $description = 'Process new support mailbox messages and send safe automatic replies.';

    public function handle(SupportInboxService $inbox): int
    {
        $result = $inbox->process((bool) $this->option('bootstrap'));
        $this->info("Processed {$result['processed']}; replied {$result['replied']}; escalated {$result['escalated']}; baselined " . ($result['baselined'] ?? 0) . '.');
        return self::SUCCESS;
    }
}
