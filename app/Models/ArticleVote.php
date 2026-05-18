<?php

namespace App\Models;

use App\Enums\VoteType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleVote extends Model
{
    protected $fillable = [
        'article_id',
        'user_id',
        'vote_type',
    ];

    protected function casts(): array
    {
        return [
            'vote_type' => VoteType::class,
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
