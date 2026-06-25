<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted publisher domains
    |--------------------------------------------------------------------------
    |
    | When Google Fact Check returns no matching claim, articles from these
    | domains receive a fallback credibility score (0–100).
    |
    */

    'trusted_domains' => [
        'politifact.com' => 82,
        'snopes.com' => 80,
        'factcheck.org' => 82,
        'apnews.com' => 78,
        'reuters.com' => 78,
        'fullfact.org' => 80,
        'checkyourfact.com' => 75,
        'leadstories.com' => 75,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default score when no fact-check match and no trusted domain
    |--------------------------------------------------------------------------
    */

    'default_neutral_score' => 50,

    /*
    |--------------------------------------------------------------------------
    | Score for .gov / .edu domains when no fact-check match
    |--------------------------------------------------------------------------
    */

    'gov_edu_score' => 72,

    /*
    |--------------------------------------------------------------------------
    | Random trust score range for new, unknown publisher domains
    |--------------------------------------------------------------------------
    |
    | Each unknown domain is assigned a random score in this range the first
    | time it is seen, then stored on the sources table for consistency.
    |
    */

    'unknown_domain_trust_score_min' => 35,
    'unknown_domain_trust_score_max' => 65,

    /*
    |--------------------------------------------------------------------------
    | Maximum Google Fact Check API queries per article
    |--------------------------------------------------------------------------
    */

    'max_fact_check_queries' => 6,

];
