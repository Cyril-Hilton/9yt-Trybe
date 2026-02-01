<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Services\Growth\AiGrowthService;
use Illuminate\Console\Command;

class GenerateAiSocialSnippets extends Command
{
    protected $signature = 'ai:growth-social-snippets {--limit=}';

    protected $description = 'Generate AI social snippets for upcoming events.';

    public function handle(AiGrowthService $growth): int
    {
        $limit = (int) ($this->option('limit') ?: config('services.ai.growth.social_limit', 30));

        $events = Event::approved()
            ->upcoming()
            ->orderBy('start_date')
            ->limit($limit)
            ->get();

        $generated = 0;
        foreach ($events as $event) {
            $insight = $growth->generateSocialSnippets($event);
            if ($insight) {
                $generated++;
            }
        }

        $this->info("Social snippets generated: {$generated}");

        return Command::SUCCESS;
    }
}
