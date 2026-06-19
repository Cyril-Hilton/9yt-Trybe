<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Event;
use App\Services\Blog\AiBlogService;
use App\Support\MediaUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class SeoAutomationTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_events_receive_immediate_seo_metadata_and_are_queued_for_indexing(): void
    {
        $event = Event::factory()->create([
            'title' => 'Accra Future Music Festival',
            'summary' => null,
            'overview' => null,
            'region' => 'Greater Accra',
            'venue_name' => 'Independence Square',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(5)->addHours(4),
            'status' => 'approved',
            'meta_title' => null,
            'meta_description' => null,
            'ai_tags' => null,
            'ai_faqs' => null,
        ])->fresh();

        $this->assertNotEmpty($event->meta_title);
        $this->assertNotEmpty($event->meta_description);
        $this->assertContains('Greater Accra', $event->ai_tags);
        $this->assertNotEmpty($event->ai_faqs);
        $this->assertContains($event->public_url, Cache::get('seo:indexnow:pending_urls', []));
    }

    public function test_new_events_can_create_a_factual_roundup_without_an_ai_provider(): void
    {
        Event::factory()->create([
            'title' => 'Accra Design Weekend',
            'region' => 'Greater Accra',
            'venue_name' => 'National Theatre',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(7)->addHours(5),
            'approved_at' => now(),
            'status' => 'approved',
        ]);

        $blog = Mockery::mock(AiBlogService::class);
        $blog->shouldReceive('generateWhatsOn')->once()->andReturnNull();
        $this->app->instance(AiBlogService::class, $blog);

        $this->artisan('ai:generate-blog-posts', [
            '--count' => 1,
            '--type' => 'whats-on',
            '--auto-publish' => true,
            '--only-if-new-events' => true,
        ])->assertSuccessful();

        $article = Article::where('category', 'whats-on')->firstOrFail();
        $this->assertTrue($article->is_published);
        $this->assertStringContainsString('Accra Design Weekend', $article->content);
    }

    public function test_public_media_paths_are_not_forced_under_storage(): void
    {
        $this->assertSame(
            asset('ui/logo/9yt-trybe-logo-light.png'),
            MediaUrl::fromPath('ui/logo/9yt-trybe-logo-light.png')
        );
        $this->assertSame(
            asset('storage/events/example.jpg'),
            MediaUrl::fromPath('events/example.jpg')
        );
    }
}
