<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ArticleContentFetcher
{
    /**
     * @return array{title: string|null, text: string}
     */
    public function fromUrl(string $url): array
    {
        $response = Http::timeout(20)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
            ])
            ->get($url);

        if (! $response->ok()) {
            return ['title' => null, 'text' => ''];
        }

        // Cap at 500 KB — article text is always in the first portion of the page.
        // Larger pages (3–4 MB single-page apps) cause PCRE backtracking failures.
        $html = substr($response->body(), 0, 512_000);

        $title = null;
        if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $m)) {
            $title = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5);
        }

        // Remove <script> and <style> blocks before stripping tags,
        // otherwise their contents (JS/CSS code) end up as plain text.
        $clean = preg_replace('/<(script|style)[^>]*>.*?<\/\1>/si', '', $html) ?? $html;

        $text = strip_tags($clean);
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';
        $text = Str::limit(trim($text), 50_000, '');

        return ['title' => $title, 'text' => $text];
    }
}
