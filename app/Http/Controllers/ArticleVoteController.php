<?php

namespace App\Http\Controllers;

use App\Enums\VoteType;
use App\Models\Article;
use App\Models\ArticleVote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArticleVoteController extends Controller
{
    public function store(Request $request, Article $article): RedirectResponse
    {
        $data = $request->validate([
            'vote_type' => ['required', 'in:'.VoteType::Real->value.','.VoteType::Fake->value],
        ]);

        ArticleVote::query()->updateOrCreate(
            [
                'article_id' => $article->id,
                'user_id' => $request->user()->id,
            ],
            [
                'vote_type' => VoteType::from($data['vote_type']),
            ]
        );

        return back()->with('status', 'Vote saved.');
    }
}
