<?php

namespace App\Services;

class FactCheckCredibilityScorer
{
    /**
     * @param  array<string, mixed>|null  $apiPayload
     * @return array{score: float|null, verdict: string|null}
     */
    public function score(?array $apiPayload): array
    {
        if ($apiPayload === null) {
            return ['score' => null, 'verdict' => null];
        }

        $claims = $apiPayload['claims'] ?? [];
        if (! is_array($claims) || $claims === []) {
            return ['score' => null, 'verdict' => 'no_match'];
        }

        $scores = [];
        foreach ($claims as $claim) {
            foreach ($claim['claimReview'] ?? [] as $review) {
                $rating = $review['textualRating'] ?? null;
                if (is_string($rating) && $rating !== '') {
                    $scores[] = $this->ratingToScore($rating);
                }
            }
        }

        if ($scores === []) {
            return ['score' => null, 'verdict' => 'unmatched_reviews'];
        }

        $avg = round(array_sum($scores) / count($scores), 2);

        $verdict = match (true) {
            $avg >= 70 => 'mostly_supported',
            $avg >= 40 => 'mixed',
            default => 'mostly_disputed',
        };

        return ['score' => $avg, 'verdict' => $verdict];
    }

    private function ratingToScore(string $rating): float
    {
        $n = strtolower($rating);

        return match (true) {
            str_contains($n, 'pants') => 5,
            str_contains($n, 'mostly false') => 22,
            str_contains($n, 'false') || str_contains($n, 'fake') || str_contains($n, 'incorrect') => 12,
            str_contains($n, 'misleading') => 28,
            str_contains($n, 'unproven') || str_contains($n, 'unclear') => 45,
            str_contains($n, 'mixed') => 50,
            str_contains($n, 'half true') || str_contains($n, 'half') => 55,
            str_contains($n, 'mostly true') => 78,
            (str_contains($n, 'true') && ! str_contains($n, 'false')) => 90,
            default => 50,
        };
    }
}
