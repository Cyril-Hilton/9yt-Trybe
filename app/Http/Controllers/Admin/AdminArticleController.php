<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminArticleController extends Controller
{
    public function index()
    {
        $articles = Article::latest()->paginate(10);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'source_name' => 'nullable|string',
            'source_url' => 'nullable|url',
            'type' => 'nullable|in:news,blog',
            'category' => 'nullable|string|max:80',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
        ]);

        $slug = Str::slug($request->title);
        $count = Article::where('slug', 'like', $slug . '%')->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
        }

        Article::create([
            'title' => $request->title,
            'slug' => $slug,
            'type' => $request->input('type', 'blog'),
            'category' => $request->input('category'),
            'description' => $request->summary,
            'content' => $request->content,
            'image_path' => $imagePath,
            'source_name' => $request->source_name ?? '9yt !Trybe',
            'source_url' => $request->source_url ?? url('/'),
            'author' => auth('admin')->user()->name ?? 'Admin',
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
            'is_published' => $request->has('is_published'),
            'published_at' => $request->has('is_published') ? now() : null,
        ]);

        return redirect()->route('admin.articles.index')->with('success', 'Article created successfully.');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'source_name' => 'nullable|string',
            'source_url' => 'nullable|url',
            'type' => 'nullable|in:news,blog',
            'category' => 'nullable|string|max:80',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('image')) {
            if ($article->image_path) {
                Storage::disk('public')->delete($article->image_path);
            }
            $article->image_path = $request->file('image')->store('articles', 'public');
        }

        $article->update([
            'title' => $request->title,
            'type' => $request->input('type', $article->type ?? 'blog'),
            'category' => $request->input('category'),
            'description' => $request->summary,
            'content' => $request->content,
            'image_path' => $article->image_path,
            'source_name' => $request->source_name ?? $article->source_name,
            'source_url' => $request->source_url ?? $article->source_url,
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
            'is_published' => $request->has('is_published'),
            'published_at' => $request->has('is_published') ? ($article->published_at ?? now()) : null,
        ]);

        return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article)
    {
        if ($article->image_path) {
            Storage::disk('public')->delete($article->image_path);
        }
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', 'Article deleted successfully.');
    }
}
