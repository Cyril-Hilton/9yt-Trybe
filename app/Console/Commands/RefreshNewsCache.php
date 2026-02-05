<?php

namespace App\Console\Commands;

use App\Services\News\NewsService;
use App\Services\AI\AiContentService;
use Illuminate\Console\Command;

class RefreshNewsCache extends Command
{
    protected $signature = 'news:refresh-cache {--query=}';

    protected $description = 'Refresh cached news articles for the public news page.';

    public function handle(NewsService $newsService, AiContentService $aiContent): int
    {
        $query = $this->option('query');
        $this->info('Refreshing news cache...');
        $articles = $newsService->warmCache($query ?: null);
        $this->info('Cached articles: ' . count($articles));

        if (count($articles) > 0) {
            $this->info('Refreshing AI news digest...');
            $aiContent->generateNewsDigest($articles, $query ?: null);
            $this->info('AI news digest cached.');
        }

        return Command::SUCCESS;
    }
}
