<?php

namespace App\Models;

use App\Enums\ArticleStatus;
use App\Enums\ArticleSubmissionType;
use App\Enums\VoteType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Article extends Model
{
    protected $fillable = [
        'user_id',
        'source_id',
        'badge_id',
        'submission_type',
        'url',
        'title',
        'content',
        'content_hash',
        'category',
        'credibility_score',
        'status',
        'duplicate_of_id',
        'processed_at',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'submission_type' => ArticleSubmissionType::class,
            'status' => ArticleStatus::class,
            'credibility_score' => 'decimal:2',
            'processed_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'duplicate_of_id');
    }

    public function factCheckResult(): HasOne
    {
        return $this->hasOne(FactCheckResult::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ArticleVote::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ArticleReport::class);
    }

    public function realVoteCount(): int
    {
        return $this->votes()->where('vote_type', VoteType::Real->value)->count();
    }

    public function fakeVoteCount(): int
    {
        return $this->votes()->where('vote_type', VoteType::Fake->value)->count();
    }
}
