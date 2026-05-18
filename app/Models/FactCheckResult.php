<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactCheckResult extends Model
{
    protected $fillable = [
        'article_id',
        'api_response',
        'computed_score',
        'verdict',
    ];

    protected function casts(): array
    {
        return [
            'api_response' => 'array',
            'computed_score' => 'decimal:2',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function claimReviews(): array
    {
        $claims = $this->api_response['claims'] ?? [];

        $out = [];
        foreach ($claims as $claim) {
            foreach ($claim['claimReview'] ?? [] as $review) {
                $out[] = [
                    'title' => $review['title'] ?? null,
                    'url' => $review['url'] ?? null,
                    'publisher' => $review['publisher']['name'] ?? null,
                    'rating' => $review['textualRating'] ?? null,
                ];
            }
        }

        return $out;
    }
}
