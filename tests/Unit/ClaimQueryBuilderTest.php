<?php

namespace Tests\Unit;

use App\Services\ClaimQueryBuilder;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClaimQueryBuilderTest extends TestCase
{
    #[Test]
    public function it_builds_title_and_sentence_queries_without_title_plus_full_body_blob(): void
    {
        config(['truthlens.max_fact_check_queries' => 6]);

        $builder = new ClaimQueryBuilder;

        $title = 'American history myths for 250th anniversary debunked';
        $content = 'George Washington did not have wooden teeth. This is a common myth. '
            .'Another sentence that is long enough to be picked as a claim query for testing.';

        $queries = $builder->build($title, $content);

        $this->assertContains($title, $queries);
        $this->assertNotContains(trim($title.' '.$content), $queries);
        $this->assertGreaterThanOrEqual(2, count($queries));
        $this->assertLessThanOrEqual(6, count($queries));
    }
}
