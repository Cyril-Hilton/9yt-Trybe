<?php

namespace App\Console\Commands;

use App\Services\Growth\AiGrowthService;
use Illuminate\Console\Command;

class GenerateAiGrowthDigest extends Command
{
    protected $signature = 'ai:growth-digest';

    protected $description = 'Generate weekly AI growth digest.';

    public function handle(AiGrowthService $growth): int
    {
        $insight = $growth->generateWeeklyDigest();

        if (!$insight) {
            $this->warn('No digest generated.');
            return Command::SUCCESS;
        }

        $this->info('Weekly growth digest generated.');

        return Command::SUCCESS;
    }
}
