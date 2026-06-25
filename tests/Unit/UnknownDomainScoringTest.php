<?php

namespace Tests\Unit;

use App\Support\UnknownDomainScoring;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UnknownDomainScoringTest extends TestCase
{
    #[Test]
    public function it_returns_scores_within_the_configured_range(): void
    {
        config([
            'truthlens.unknown_domain_trust_score_min' => 40,
            'truthlens.unknown_domain_trust_score_max' => 60,
        ]);

        for ($i = 0; $i < 20; $i++) {
            $score = UnknownDomainScoring::randomTrustScore();

            $this->assertGreaterThanOrEqual(40, $score);
            $this->assertLessThanOrEqual(60, $score);
        }
    }

    #[Test]
    public function it_swaps_min_and_max_when_config_is_inverted(): void
    {
        config([
            'truthlens.unknown_domain_trust_score_min' => 70,
            'truthlens.unknown_domain_trust_score_max' => 50,
        ]);

        $score = UnknownDomainScoring::randomTrustScore();

        $this->assertGreaterThanOrEqual(50, $score);
        $this->assertLessThanOrEqual(70, $score);
    }
}
