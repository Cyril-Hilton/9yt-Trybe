<?php

namespace App\Console\Commands;

use App\Models\SmsCampaign;
use App\Services\Sms\SmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendScheduledSmsCampaigns extends Command
{
    protected $signature = 'sms:send-scheduled-campaigns {--limit=25}';
    protected $description = 'Send scheduled SMS campaigns that are due';

    public function handle(SmsService $smsService): int
    {
        $limit = max(1, (int) $this->option('limit'));

        $campaigns = SmsCampaign::scheduled()
            ->with('owner')
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get();

        if ($campaigns->isEmpty()) {
            $this->info('No scheduled campaigns due.');
            Log::debug('SMS scheduled check: No campaigns due.');
            return 0;
        }

        foreach ($campaigns as $campaign) {
            DB::beginTransaction();
            try {
                $campaign->refresh();
                if ($campaign->status !== 'scheduled') {
                    DB::rollBack();
                    continue;
                }

                $result = $smsService->sendScheduledCampaign($campaign);
                DB::commit();

                if (!$result['success']) {
                    $this->warn("Campaign {$campaign->id} failed: {$result['error']}");
                } else {
                    $this->info("Campaign {$campaign->id} sent to {$result['sent_count']} recipients.");
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Scheduled SMS send failed', [
                    'campaign_id' => $campaign->id,
                    'error' => $e->getMessage(),
                ]);

                $this->error("Campaign {$campaign->id} failed with error: {$e->getMessage()}");
            }
        }

        return 0;
    }
}
