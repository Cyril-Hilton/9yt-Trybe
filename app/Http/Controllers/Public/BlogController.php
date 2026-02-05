<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $category = trim((string) $request->input('category', ''));

        $query = Article::where('is_published', true)
            ->where('type', 'blog');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        $articles = $query->orderBy('published_at', 'desc')
            ->paginate(9)
            ->withQueryString();

        $categories = Article::where('type', 'blog')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('public.blog.index', [
            'articles' => $articles,
            'categories' => $categories,
            'search' => $search,
            'category' => $category,
        ]);
    }

    public function show(string $slug)
    {
        $article = Article::where('slug', $slug)
            ->where('type', 'blog')
            ->where('is_published', true)
            ->firstOrFail();

        $related = Article::where('type', 'blog')
            ->where('is_published', true)
            ->where('id', '!=', $article->id)
            ->when($article->category, function ($q) use ($article) {
                $q->where('category', $article->category);
            })
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('public.blog.show', [
            'article' => $article,
            'related' => $related,
        ]);
    }

    public function rss()
    {
        $articles = Article::where('is_published', true)
            ->where('type', 'blog')
            ->orderBy('published_at', 'desc')
            ->limit(20)
            ->get();

        $items = $articles->map(function ($article) {
            $title = e($article->title);
            $link = url('/blog/' . $article->slug);
            $description = e(Str::limit(strip_tags($article->description), 300));
            $pubDate = $article->published_at ? $article->published_at->toRfc2822String() : now()->toRfc2822String();

            return "<item><title>{$title}</title><link>{$link}</link><guid>{$link}</guid><description>{$description}</description><pubDate>{$pubDate}</pubDate></item>";
        })->implode('');

        $rss = '<?xml version="1.0" encoding="UTF-8"?>';
        $rss .= '<rss version="2.0"><channel>';
        $rss .= '<title>9yt !Trybe Blog</title>';
        $rss .= '<link>' . url('/blog') . '</link>';
        $rss .= '<description>How-tos and whats-on from 9yt !Trybe</description>';
        $rss .= '<language>en</language>';
        $rss .= $items;
        $rss .= '</channel></rss>';

        return response($rss, 200)->header('Content-Type', 'application/rss+xml');
    }
}
