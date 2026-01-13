<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Conference;
use App\Models\Event;
use App\Models\Poll;
use App\Models\ShopProduct;
use App\Models\Survey;

class SitemapController extends Controller
{
    /**
     * Generate XML sitemap for search engines
     */
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $sitemap .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        // Static public pages (only URLs that exist and should be indexed)
        $staticPages = [
            ['/', now(), 'daily', '1.0'],
            ['/events', now(), 'daily', '0.9'],
            ['/events/calendar', now(), 'weekly', '0.7'],
            ['/gallery', now(), 'weekly', '0.6'],
            ['/shop', now(), 'weekly', '0.7'],
            ['/organizers', now(), 'weekly', '0.6'],
            ['/polls', now(), 'weekly', '0.6'],
            ['/jobs', now(), 'monthly', '0.4'],
            ['/team', now(), 'monthly', '0.4'],
            ['/about', now(), 'monthly', '0.3'],
            ['/contact', now(), 'monthly', '0.3'],
            ['/fee-calculator', now(), 'monthly', '0.3'],
            ['/terms-and-conditions', now(), 'yearly', '0.2'],
            ['/privacy-policy', now(), 'yearly', '0.2'],
            ['/cookie-policy', now(), 'yearly', '0.2'],
            ['/refund-policy', now(), 'yearly', '0.2'],
            ['/disclaimer', now(), 'yearly', '0.2'],
        ];

        foreach ($staticPages as [$path, $lastmod, $changefreq, $priority]) {
            $sitemap .= $this->addUrl($path, $lastmod, $changefreq, $priority);
        }

        // Events
        $events = Event::approved()
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($events as $event) {
            $url = "/events/{$event->slug}";
            $image = $event->flier_path ? asset('storage/' . $event->flier_path) : null;
            $sitemap .= $this->addUrl($url, $event->updated_at, 'weekly', '0.8', $image);
        }

        // Categories
        $categories = Category::active()
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($categories as $category) {
            $url = "/categories/{$category->slug}";
            $sitemap .= $this->addUrl($url, $category->updated_at, 'weekly', '0.6');
        }

        // Organizers
        $organizers = Company::where(function ($q) {
                $q->where('is_suspended', false)->orWhereNull('is_suspended');
            })
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($organizers as $organizer) {
            $url = "/organizers/{$organizer->slug}";
            $sitemap .= $this->addUrl($url, $organizer->updated_at, 'weekly', '0.6');
        }

        // Conferences (public registration)
        $conferences = Conference::where('status', 'active')
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($conferences as $conference) {
            $url = "/register/{$conference->slug}";
            $sitemap .= $this->addUrl($url, $conference->updated_at, 'weekly', '0.7');
        }

        // Surveys
        $surveys = Survey::where('status', 'active')
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($surveys as $survey) {
            $url = "/survey/{$survey->slug}";
            $sitemap .= $this->addUrl($url, $survey->updated_at, 'weekly', '0.5');
        }

        // Polls
        $polls = Poll::where('status', 'active')
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($polls as $poll) {
            $url = "/polls/{$poll->slug}";
            $sitemap .= $this->addUrl($url, $poll->updated_at, 'weekly', '0.6');
        }

        // Shop products
        $products = ShopProduct::where('status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($products as $product) {
            $url = "/shop/{$product->slug}";
            $image = $product->image_path ? asset('storage/' . $product->image_path) : null;
            $sitemap .= $this->addUrl($url, $product->updated_at, 'weekly', '0.6', $image);
        }

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Helper method to add URL to sitemap
     */
    private function addUrl($path, $lastmod = null, $changefreq = 'weekly', $priority = '0.5', $image = null)
    {
        $url = '<url>';
        $url .= '<loc>' . url($path) . '</loc>';

        if ($lastmod) {
            $url .= '<lastmod>' . $lastmod->toAtomString() . '</lastmod>';
        }

        $url .= '<changefreq>' . $changefreq . '</changefreq>';
        $url .= '<priority>' . $priority . '</priority>';

        if ($image) {
            $url .= '<image:image>';
            $url .= '<image:loc>' . $image . '</image:loc>';
            $url .= '</image:image>';
        }

        $url .= '</url>';

        return $url;
    }
}
