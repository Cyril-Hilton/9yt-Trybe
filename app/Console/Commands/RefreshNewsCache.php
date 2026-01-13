<?php

namespace App\Console\Commands;

use App\Services\News\NewsService;
use Illuminate\Console\Command;

class RefreshNewsCache extends Command
{
    protected $signature = 'news:refresh-cache {--query=}';

    protected $description = 'Refresh cached news articles for the public news page.';

    public function handle(NewsService $newsService): int
    {
        $query = $this->option('query');
        $articles = $newsService->warmCache($query ?: null);

        $this->info('Cached articles: ' . count($articles));

        return Command::SUCCESS;
    }
}
