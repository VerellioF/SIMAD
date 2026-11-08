<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::with('author')->latest()->paginate(15);

        return view('admin.articles.index', compact('articles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'is_published' => ['required', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'thumbnail' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('articles', 'public');
        }

        Article::create([
            'author_id' => $admin->id,
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(5),
            'content' => $validated['content'],
            'thumbnail' => $thumbnailPath,
            'is_published' => (bool) $validated['is_published'],
            'published_at' => $validated['published_at'] ?? now(),
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', "Warta / Artikel \"{$validated['title']}\" berhasil diterbitkan.");
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'is_published' => ['required', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'thumbnail' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ]);

        $updateData = [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_published' => (bool) $validated['is_published'],
            'published_at' => $validated['published_at'] ?? $article->published_at,
        ];

        if ($request->hasFile('thumbnail')) {
            $updateData['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        }

        $article->update($updateData);

        return redirect()->route('admin.articles.index')
            ->with('success', "Artikel \"{$article->title}\" berhasil diperbarui.");
    }

    public function destroy(Article $article): RedirectResponse
    {
        $title = $article->title;
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', "Artikel \"{$title}\" berhasil dihapus.");
    }
}
