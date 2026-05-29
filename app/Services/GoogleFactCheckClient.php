<?php

namespace App\Services;

use App\Support\OutboundHttp;

class GoogleFactCheckClient
{
    public function searchClaims(string $query): ?array
    {
        $key = config('services.google_fact_check.key');
        if (! is_string($key) || $key === '') {
            return null;
        }

        $language = config('services.google_fact_check.language', 'en-US');

        $response = OutboundHttp::client()
            ->timeout(25)
            ->acceptJson()
            ->get('https://factchecktools.googleapis.com/v1alpha1/claims:search', [
                'key' => $key,
                'query' => $query,
                'languageCode' => $language,
                'pageSize' => 10,
            ]);

        if (! $response->ok()) {
            return null;
        }

        /** @var array<string, mixed> */
        $json = $response->json();

        return $json;
    }
}
