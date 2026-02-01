<?php

namespace App\Observers;

use App\Models\Article;
use App\Services\SEO\IndexNowService;

class ArticleObserver
{
    public function created(Article $article): void
    {
        $this->submitIfPublished($article);
    }

    public function updated(Article $article): void
    {
        if ($article->wasChanged(['is_published', 'slug'])) {
            $this->submitIfPublished($article);
        }
    }

    private function submitIfPublished(Article $article): void
    {
        if (!$article->is_published) {
            return;
        }

        if (($article->type ?? 'news') !== 'blog') {
            return;
        }

        $url = url('/blog/' . $article->slug);
        app(IndexNowService::class)->submitUrls([$url]);
    }
}
