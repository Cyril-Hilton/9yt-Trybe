<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\Growth\AiGrowthService;
use Illuminate\Console\Command;

class GenerateAiOrganizerTips extends Command
{
    protected $signature = 'ai:growth-organizer-tips {--limit=}';

    protected $description = 'Generate AI optimization tips for organizers.';

    public function handle(AiGrowthService $growth): int
    {
        $limit = (int) ($this->option('limit') ?: config('services.ai.growth.organizer_limit', 20));

        $companies = Company::where(function ($q) {
                $q->where('is_suspended', false)->orWhereNull('is_suspended');
            })
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $generated = 0;
        foreach ($companies as $company) {
            $insight = $growth->generateOrganizerTips($company);
            if ($insight) {
                $generated++;
            }
        }

        $this->info("Organizer tips generated: {$generated}");

        return Command::SUCCESS;
    }
}
