<?php

namespace App\Services;

use Illuminate\Support\Str;

class ClaimQueryBuilder
{
    /**
     * Build short, claim-like search queries for the Google Fact Check API.
     *
     * @return list<string>
     */
    public function build(string $title, string $content): array
    {
        $queries = [];

        $title = trim($title);
        if ($title !== '') {
            $queries[] = Str::limit($title, 500, '');

            foreach (preg_split('/[:\-|–—]/u', $title) ?: [] as $part) {
                $part = trim($part);
                if (strlen($part) >= 20) {
                    $queries[] = Str::limit($part, 500, '');
                }
            }
        }

        $content = trim($content);
        if ($content !== '') {
            $queries[] = Str::limit($content, 300, '');

            foreach ($this->extractSentences($content) as $sentence) {
                $queries[] = Str::limit($sentence, 500, '');

                if (count($queries) >= 12) {
                    break;
                }
            }
        }

        $max = (int) config('truthlens.max_fact_check_queries', 6);

        return array_slice(
            array_values(array_unique(array_filter($queries, fn (string $q) => strlen(trim($q)) >= 15))),
            0,
            $max,
        );
    }

    /**
     * @return list<string>
     */
    private function extractSentences(string $content): array
    {
        $sentences = preg_split('/(?<=[.!?])\s+/u', $content, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $picked = [];

        foreach ($sentences as $sentence) {
            $sentence = trim(preg_replace('/\s+/u', ' ', $sentence) ?? '');
            $length = strlen($sentence);

            if ($length < 40 || $length > 500) {
                continue;
            }

            $picked[] = $sentence;

            if (count($picked) >= 5) {
                break;
            }
        }

        return $picked;
    }
}
