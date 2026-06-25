<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'min_score',
        'max_score',
    ];

    protected function casts(): array
    {
        return [
            'min_score' => 'integer',
            'max_score' => 'integer',
        ];
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public static function findForScore(?float $score): ?self
    {
        if ($score === null) {
            $score = (float) config('truthlens.default_neutral_score', 50);
        }

        return self::query()
            ->where('slug', '!=', 'unverified')
            ->where(function ($q) use ($score) {
                $q->where(function ($q2) use ($score) {
                    $q2->whereNotNull('min_score')
                        ->whereNotNull('max_score')
                        ->where('min_score', '<=', $score)
                        ->where('max_score', '>=', $score);
                });
            })
            ->orderByDesc('min_score')
            ->first();
    }
}
