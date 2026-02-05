<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Event;
use App\Services\Blog\AiBlogService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GenerateAiBlogPosts extends Command
{
    protected $signature = 'ai:generate-blog-posts {--count=} {--auto-publish} {--type=}';

    protected $description = 'Generate blog posts using AI (how-to and whats-on).';

    public function handle(AiBlogService $blog): int
    {
        $count = (int) ($this->option('count') ?: config('services.ai.blog.daily_count', 2));
        $autoPublish = (bool) $this->option('auto-publish');
        if (!$autoPublish) {
            $autoPublish = filter_var(config('services.ai.blog.auto_publish', false), FILTER_VALIDATE_BOOLEAN);
        }

        $typeOption = trim((string) $this->option('type'));
        $types = $typeOption !== '' ? [strtolower($typeOption)] : $this->buildTypes($count);

        $created = 0;
        foreach ($types as $type) {
            $result = $type === 'whats-on'
                ? $this->createWhatsOn($blog, $autoPublish)
                : $this->createHowTo($blog, $autoPublish);

            if ($result) {
                $created++;
                $this->info("Created: {$result}");
            }
        }

        $this->info("AI blog generation complete. New posts: {$created}");

        return Command::SUCCESS;
    }

    private function buildTypes(int $count): array
    {
        $types = [];
        if ($count >= 1) {
            $types[] = 'how-to';
        }
        if ($count >= 2) {
            $types[] = 'whats-on';
        }
        while (count($types) < $count) {
            $types[] = count($types) % 2 === 0 ? 'how-to' : 'whats-on';
        }

        return $types;
    }

    private function createHowTo(AiBlogService $blog, bool $autoPublish): ?string
    {
        $topics = $this->getHowToTopics();
        if (empty($topics)) {
            return null;
        }

        $topicIndex = (int) Cache::get('ai:blog:how_to_index', 0);
        $topic = $topics[$topicIndex % count($topics)];
        Cache::put('ai:blog:how_to_index', $topicIndex + 1, now()->addDays(30));

        $context = [
            'features' => [
                'Ticket sales with paystack checkout',
                'Bulk SMS campaigns',
                'Polls and voting',
                'Surveys and feedback',
                'Event analytics and attendee management',
            ],
        ];

        $data = $blog->generateHowTo($topic, $context);
        if (!$data) {
            return null;
        }

        return $this->storeArticle($data, $autoPublish, 'blog');
    }

    private function createWhatsOn(AiBlogService $blog, bool $autoPublish): ?string
    {
        $region = $this->getWhatsOnRegion();
        $events = $this->getUpcomingEventsForRegion($region);

        if (empty($events)) {
            $region = $region ?: 'Ghana';
            $events = $this->getUpcomingEventsAnyRegion();
        }

        if (empty($events)) {
            return null;
        }

        $data = $blog->generateWhatsOn($region, $events);
        if (!$data) {
            return null;
        }

        return $this->storeArticle($data, $autoPublish, 'blog');
    }

    private function storeArticle(array $data, bool $autoPublish, string $type): ?string
    {
        $title = $data['title'] ?? null;
        if (!$title) {
            return null;
        }

        $slugBase = Str::slug($title);
        if ($slugBase === '') {
            return null;
        }

        $slug = $slugBase;
        $count = Article::where('slug', 'like', $slugBase . '%')->count();
        if ($count > 0) {
            $slug = $slugBase . '-' . ($count + 1);
        }

        $recentExists = Article::where('type', $type)
            ->where('title', $title)
            ->where('created_at', '>=', now()->subDays(30))
            ->exists();

        if ($recentExists) {
            return null;
        }

        $article = Article::create([
            'title' => $title,
            'slug' => $slug,
            'type' => $type,
            'category' => $data['category'] ?? null,
            'description' => $data['summary'] ?? '',
            'content' => $data['content'] ?? '',
            'image_path' => null,
            'source_name' => '9yt !Trybe Blog',
            'source_url' => config('app.url') . '/blog',
            'author' => 'AI',
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'is_published' => $autoPublish,
            'published_at' => $autoPublish ? now() : null,
        ]);

        return $article->title;
    }

    private function getHowToTopics(): array
    {
        $topics = config('services.ai.blog.how_to_topics', []);

        return array_values(array_filter($topics, fn ($item) => is_string($item) && trim($item) !== ''));
    }

    private function getWhatsOnRegion(): string
    {
        $regions = config('services.ai.blog.whats_on_regions', []);
        $regions = array_values(array_filter($regions, fn ($item) => is_string($item) && trim($item) !== ''));

        if (empty($regions)) {
            return '';
        }

        $index = (int) Cache::get('ai:blog:whats_on_region_index', 0);
        $region = $regions[$index % count($regions)];
        Cache::put('ai:blog:whats_on_region_index', $index + 1, now()->addDays(30));

        return $region;
    }

    private function getUpcomingEventsForRegion(string $region): array
    {
        if ($region === '') {
            return [];
        }

        return Event::approved()
            ->upcoming()
            ->where('region', $region)
            ->whereBetween('start_date', [now(), now()->addDays(7)])
            ->orderBy('start_date')
            ->limit(6)
            ->get()
            ->map(function ($event) {
                return [
                    'title' => $event->title,
                    'date' => $event->start_date?->format('M d, Y'),
                    'venue' => $event->venue_name ?? ($event->location_type === 'online' ? 'Online' : ''),
                    'url' => $event->public_url,
                ];
            })
            ->toArray();
    }

    private function getUpcomingEventsAnyRegion(): array
    {
        return Event::approved()
            ->upcoming()
            ->whereBetween('start_date', [now(), now()->addDays(7)])
            ->orderBy('start_date')
            ->limit(6)
            ->get()
            ->map(function ($event) {
                return [
                    'title' => $event->title,
                    'date' => $event->start_date?->format('M d, Y'),
                    'venue' => $event->venue_name ?? ($event->location_type === 'online' ? 'Online' : ''),
                    'url' => $event->public_url,
                ];
            })
            ->toArray();
    }
}
