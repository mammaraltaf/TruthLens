<?php

namespace App\Support;

class UnknownDomainScoring
{
    public static function randomTrustScore(): int
    {
        $min = (int) config('truthlens.unknown_domain_trust_score_min', 35);
        $max = (int) config('truthlens.unknown_domain_trust_score_max', 65);

        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        return random_int($min, $max);
    }
}
