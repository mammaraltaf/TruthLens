<?php

namespace Tests\Unit;

use App\Models\Badge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BadgeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\BadgeSeeder::class);
    }
    #[Test]
    public function it_maps_null_score_to_default_tier_instead_of_unverified(): void
    {
        config(['truthlens.default_neutral_score' => 50]);

        $badge = Badge::findForScore(null);

        $this->assertNotNull($badge);
        $this->assertNotSame('unverified', $badge->slug);
        $this->assertSame('suspicious', $badge->slug);
    }
}
