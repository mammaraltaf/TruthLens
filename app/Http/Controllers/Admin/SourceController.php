<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SourceController extends Controller
{
    public function index(Request $request): View
    {
        $sources = Source::query()
            ->withCount('articles')
            ->when(
                $request->boolean('banned'),
                fn ($q) => $q->where('is_banned', true),
            )
            ->orderByDesc('article_count')
            ->orderBy('domain')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Source::query()->count(),
            'banned' => Source::query()->where('is_banned', true)->count(),
        ];

        return view('admin.sources.index', compact('sources', 'stats'));
    }

    public function update(Request $request, Source $source): RedirectResponse
    {
        $validated = $request->validate([
            'is_banned' => ['required', 'boolean'],
            'trust_score' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $source->update([
            'is_banned' => $validated['is_banned'],
            'trust_score' => $validated['trust_score'] ?? $source->trust_score,
        ]);

        $message = $source->is_banned
            ? "Domain {$source->domain} has been banned."
            : "Domain {$source->domain} has been updated.";

        return back()->with('status', $message);
    }
}
