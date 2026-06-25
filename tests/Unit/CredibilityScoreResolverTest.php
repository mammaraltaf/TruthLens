<?php

namespace Tests\Unit;

use App\Models\Source;
use App\Services\CredibilityScoreResolver;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CredibilityScoreResolverTest extends TestCase
{
    #[Test]
    public function it_keeps_api_score_when_present(): void
    {
        $resolver = new CredibilityScoreResolver;

        $result = $resolver->resolve(88.5, 'mostly_supported', 'politifact.com');

        $this->assertSame(88.5, $result['score']);
        $this->assertSame('mostly_supported', $result['verdict']);
        $this->assertSame('fact_check_api', $result['score_source']);
    }

    #[Test]
    public function it_uses_trusted_domain_fallback_when_api_returns_no_match(): void
    {
        $resolver = new CredibilityScoreResolver;

        $result = $resolver->resolve(null, 'no_match', 'politifact.com');

        $this->assertSame(82.0, $result['score']);
        $this->assertSame('trusted_publisher', $result['verdict']);
        $this->assertSame('trusted_domain', $result['score_source']);
    }

    #[Test]
    public function it_uses_source_trust_when_domain_is_not_in_trusted_list(): void
    {
        $source = new Source(['domain' => 'example.com', 'trust_score' => 63]);

        $resolver = new CredibilityScoreResolver;

        $result = $resolver->resolve(null, 'no_match', 'example.com', $source);

        $this->assertSame(63.0, $result['score']);
        $this->assertSame('source_trust', $result['verdict']);
        $this->assertSame('source_trust', $result['score_source']);
    }

    #[Test]
    public function it_uses_neutral_default_for_text_submissions_without_matches(): void
    {
        config(['truthlens.default_neutral_score' => 50]);

        $resolver = new CredibilityScoreResolver;

        $result = $resolver->resolve(null, 'no_match', null);

        $this->assertSame(50.0, $result['score']);
        $this->assertSame('no_match', $result['verdict']);
        $this->assertSame('neutral_default', $result['score_source']);
    }
}
