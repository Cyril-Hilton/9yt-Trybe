<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\AI\AiContentService;
use App\Services\News\NewsService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct(
        private readonly NewsService $newsService,
        private readonly AiContentService $aiContent
    )
    {
    }

    public function index(Request $request)
    {
        $query = $request->string('q')->trim()->value();
        $articles = $this->newsService->getArticles($query ?: null);
        $aiDigest = $this->aiContent->generateNewsDigest($articles, $query ?: null);

        if ($request->wantsJson()) {
            return response()->json([
                'query' => $query,
                'count' => count($articles),
                'articles' => $articles,
                'ai_digest' => $aiDigest,
            ]);
        }

        return view('public.news.index', [
            'articles' => $articles,
            'query' => $query,
            'aiDigest' => $aiDigest,
        ]);
    }
}
