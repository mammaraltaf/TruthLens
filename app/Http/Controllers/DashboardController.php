<?php

namespace App\Http\Controllers;

use App\Enums\ArticleStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $articles = $user
            ->articles()
            ->with(['badge', 'factCheckResult'])
            ->latest()
            ->limit(25)
            ->get();

        $stats = [
            'total' => $user->articles()->count(),
            'completed' => $user->articles()->where('status', ArticleStatus::Completed)->count(),
            'in_progress' => $user->articles()->whereIn('status', [ArticleStatus::Pending, ArticleStatus::Processing])->count(),
        ];

        return view('dashboard', compact('articles', 'stats'));
    }
}
