<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Company;
use App\Models\Conference;
use App\Models\Event;
use App\Models\Poll;
use App\Models\ShopProduct;
use App\Models\Survey;
use App\Services\SEO\AiSeoService;
use Illuminate\Console\Command;

class RefreshSeoMetadata extends Command
{
    protected $signature = 'seo:refresh {--limit=} {--days=} {--only-missing} {--types=}';

    protected $description = 'Generate or refresh SEO metadata using AI.';

    public function handle(AiSeoService $seo): int
    {
        $limit = (int) ($this->option('limit') ?: config('services.ai.seo.limit', 80));
        $days = (int) ($this->option('days') ?: config('services.ai.seo.days', 30));

        $onlyMissing = (bool) $this->option('only-missing');
        if (!$onlyMissing) {
            $onlyMissing = filter_var(config('services.ai.seo.only_missing', true), FILTER_VALIDATE_BOOLEAN);
        }

        $typesOption = (string) ($this->option('types') ?: '');
        $types = $typesOption !== ''
            ? collect(explode(',', $typesOption))->map(fn ($t) => trim(strtolower($t)))->filter()->values()->all()
            : (array) config('services.ai.seo.types', ['events', 'polls', 'articles']);

        $this->info('AI SEO refresh started. Types: ' . implode(', ', $types));

        $totalUpdated = 0;

        if (in_array('events', $types, true)) {
            $updated = $this->refreshEvents($seo, $limit, $days, $onlyMissing);
            $totalUpdated += $updated;
        }

        if (in_array('polls', $types, true)) {
            $updated = $this->refreshPolls($seo, $limit, $days, $onlyMissing);
            $totalUpdated += $updated;
        }

        if (in_array('articles', $types, true)) {
            $updated = $this->refreshArticles($seo, $limit, $days, $onlyMissing);
            $totalUpdated += $updated;
        }

        if (in_array('companies', $types, true)) {
            $updated = $this->refreshCompanies($seo, $limit, $days, $onlyMissing);
            $totalUpdated += $updated;
        }

        if (in_array('products', $types, true)) {
            $updated = $this->refreshProducts($seo, $limit, $days, $onlyMissing);
            $totalUpdated += $updated;
        }

        if (in_array('surveys', $types, true)) {
            $updated = $this->refreshSurveys($seo, $limit, $days, $onlyMissing);
            $totalUpdated += $updated;
        }

        if (in_array('conferences', $types, true)) {
            $updated = $this->refreshConferences($seo, $limit, $days, $onlyMissing);
            $totalUpdated += $updated;
        }

        if (in_array('categories', $types, true)) {
            $updated = $this->refreshCategories($seo, $limit, $days, $onlyMissing);
            $totalUpdated += $updated;
        }

        $this->info('AI SEO refresh done. Updated: ' . $totalUpdated);

        return Command::SUCCESS;
    }

    private function refreshEvents(AiSeoService $seo, int $limit, int $days, bool $onlyMissing): int
    {
        $query = Event::query()
            ->where('status', 'approved');

        if ($days > 0) {
            $query->where('updated_at', '>=', now()->subDays($days));
        }

        if ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('meta_title')
                    ->orWhereNull('meta_description');
            });
        }

        $events = $query->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $updated = 0;
        foreach ($events as $event) {
            $meta = $seo->generateEventMeta($event);
            if (!$meta) {
                continue;
            }

            $event->fill($meta);
            if ($event->isDirty(['meta_title', 'meta_description'])) {
                $event->save();
                $updated++;
            }
        }

        $this->info('Events updated: ' . $updated);

        return $updated;
    }

    private function refreshPolls(AiSeoService $seo, int $limit, int $days, bool $onlyMissing): int
    {
        $query = Poll::query()
            ->where('status', 'active');

        if ($days > 0) {
            $query->where('updated_at', '>=', now()->subDays($days));
        }

        if ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('meta_title')
                    ->orWhereNull('meta_description');
            });
        }

        $polls = $query->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $updated = 0;
        foreach ($polls as $poll) {
            $meta = $seo->generatePollMeta($poll);
            if (!$meta) {
                continue;
            }

            $poll->fill($meta);
            if ($poll->isDirty(['meta_title', 'meta_description'])) {
                $poll->save();
                $updated++;
            }
        }

        $this->info('Polls updated: ' . $updated);

        return $updated;
    }

    private function refreshArticles(AiSeoService $seo, int $limit, int $days, bool $onlyMissing): int
    {
        $query = \App\Models\Article::query()
            ->where('type', 'blog')
            ->where('is_published', true);

        if ($days > 0) {
            $query->where('updated_at', '>=', now()->subDays($days));
        }

        if ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('meta_title')
                    ->orWhereNull('meta_description');
            });
        }

        $articles = $query->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $updated = 0;
        foreach ($articles as $article) {
            $meta = $seo->generateArticleMeta($article);
            if (!$meta) {
                continue;
            }

            $article->fill($meta);
            if ($article->isDirty(['meta_title', 'meta_description'])) {
                $article->save();
                $updated++;
            }
        }

        $this->info('Blog articles updated: ' . $updated);

        return $updated;
    }

    private function refreshCompanies(AiSeoService $seo, int $limit, int $days, bool $onlyMissing): int
    {
        $query = Company::query()
            ->where(function ($q) {
                $q->where('is_suspended', false)->orWhereNull('is_suspended');
            });

        if ($days > 0) {
            $query->where('updated_at', '>=', now()->subDays($days));
        }

        if ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('meta_title')
                    ->orWhereNull('meta_description');
            });
        }

        $companies = $query->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $updated = 0;
        foreach ($companies as $company) {
            $meta = $seo->generateCompanyMeta($company);
            if (!$meta) {
                continue;
            }

            $company->fill($meta);
            if ($company->isDirty(['meta_title', 'meta_description'])) {
                $company->save();
                $updated++;
            }
        }

        $this->info('Companies updated: ' . $updated);

        return $updated;
    }

    private function refreshProducts(AiSeoService $seo, int $limit, int $days, bool $onlyMissing): int
    {
        $query = ShopProduct::query()
            ->where('status', 'approved')
            ->where('is_active', true);

        if ($days > 0) {
            $query->where('updated_at', '>=', now()->subDays($days));
        }

        if ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('meta_title')
                    ->orWhereNull('meta_description');
            });
        }

        $products = $query->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $updated = 0;
        foreach ($products as $product) {
            $meta = $seo->generateProductMeta($product);
            if (!$meta) {
                continue;
            }

            $product->fill($meta);
            if ($product->isDirty(['meta_title', 'meta_description'])) {
                $product->save();
                $updated++;
            }
        }

        $this->info('Products updated: ' . $updated);

        return $updated;
    }

    private function refreshSurveys(AiSeoService $seo, int $limit, int $days, bool $onlyMissing): int
    {
        $query = Survey::query()
            ->where('status', 'active');

        if ($days > 0) {
            $query->where('updated_at', '>=', now()->subDays($days));
        }

        if ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('meta_title')
                    ->orWhereNull('meta_description');
            });
        }

        $surveys = $query->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $updated = 0;
        foreach ($surveys as $survey) {
            $meta = $seo->generateSurveyMeta($survey);
            if (!$meta) {
                continue;
            }

            $survey->fill($meta);
            if ($survey->isDirty(['meta_title', 'meta_description'])) {
                $survey->save();
                $updated++;
            }
        }

        $this->info('Surveys updated: ' . $updated);

        return $updated;
    }

    private function refreshConferences(AiSeoService $seo, int $limit, int $days, bool $onlyMissing): int
    {
        $query = Conference::query()
            ->where('status', 'active');

        if ($days > 0) {
            $query->where('updated_at', '>=', now()->subDays($days));
        }

        if ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('meta_title')
                    ->orWhereNull('meta_description');
            });
        }

        $conferences = $query->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $updated = 0;
        foreach ($conferences as $conference) {
            $meta = $seo->generateConferenceMeta($conference);
            if (!$meta) {
                continue;
            }

            $conference->fill($meta);
            if ($conference->isDirty(['meta_title', 'meta_description'])) {
                $conference->save();
                $updated++;
            }
        }

        $this->info('Conferences updated: ' . $updated);

        return $updated;
    }

    private function refreshCategories(AiSeoService $seo, int $limit, int $days, bool $onlyMissing): int
    {
        $query = Category::query()
            ->where('is_active', true);

        if ($days > 0) {
            $query->where('updated_at', '>=', now()->subDays($days));
        }

        if ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('meta_title')
                    ->orWhereNull('meta_description');
            });
        }

        $categories = $query->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $updated = 0;
        foreach ($categories as $category) {
            $meta = $seo->generateCategoryMeta($category);
            if (!$meta) {
                continue;
            }

            $category->fill($meta);
            if ($category->isDirty(['meta_title', 'meta_description'])) {
                $category->save();
                $updated++;
            }
        }

        $this->info('Categories updated: ' . $updated);

        return $updated;
    }
}
