<?php

namespace App\Services;

use App\Models\Source;

class CredibilityScoreResolver
{
    /**
     * @return array{score: float, verdict: string, score_source: string}
     */
    public function resolve(?float $apiScore, ?string $apiVerdict, ?string $host, ?Source $source = null): array
    {
        if ($apiScore !== null) {
            return [
                'score' => $apiScore,
                'verdict' => $apiVerdict ?? 'fact_check_match',
                'score_source' => 'fact_check_api',
            ];
        }

        if (is_string($host) && $host !== '') {
            $trustedDomains = config('truthlens.trusted_domains', []);
            $trustedScore = is_array($trustedDomains) ? ($trustedDomains[$host] ?? null) : null;

            if (is_numeric($trustedScore)) {
                return [
                    'score' => (float) $trustedScore,
                    'verdict' => 'trusted_publisher',
                    'score_source' => 'trusted_domain',
                ];
            }

            if (preg_match('/\.(gov|edu)$/i', $host) === 1) {
                return [
                    'score' => (float) config('truthlens.gov_edu_score', 72),
                    'verdict' => 'trusted_publisher',
                    'score_source' => 'gov_edu_domain',
                ];
            }
        }

        if ($source !== null && $source->trust_score > 0) {
            return [
                'score' => (float) $source->trust_score,
                'verdict' => 'source_trust',
                'score_source' => 'source_trust',
            ];
        }

        $neutral = (float) config('truthlens.default_neutral_score', 50);

        return [
            'score' => $neutral,
            'verdict' => $apiVerdict ?? 'no_match',
            'score_source' => 'neutral_default',
        ];
    }
}
