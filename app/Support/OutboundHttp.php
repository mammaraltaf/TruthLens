<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class OutboundHttp
{
    public static function client(): PendingRequest
    {
        return Http::withOptions(static::sslOptions());
    }

    /**
     * @return array<string, mixed>
     */
    public static function sslOptions(): array
    {
        if (! static::shouldVerifySsl()) {
            return ['verify' => false];
        }

        $configured = config('services.http.ca_bundle');
        if (is_string($configured) && $configured !== '' && is_readable($configured)) {
            return ['verify' => $configured];
        }

        $detected = static::detectCaBundle();
        if ($detected !== null) {
            return ['verify' => $detected];
        }

        return [];
    }

    public static function shouldVerifySsl(): bool
    {
        return filter_var(config('services.http.verify_ssl', true), FILTER_VALIDATE_BOOLEAN);
    }

    private static function detectCaBundle(): ?string
    {
        $candidates = [];

        foreach (['curl.cainfo', 'openssl.cafile'] as $iniKey) {
            $path = ini_get($iniKey);
            if (is_string($path) && $path !== '') {
                $candidates[] = $path;
            }
        }

        $laragonRoot = getenv('LARAGON_ROOT');
        if (is_string($laragonRoot) && $laragonRoot !== '') {
            $candidates[] = rtrim($laragonRoot, '\\/').'/etc/ssl/cacert.pem';
        }

        if (PHP_OS_FAMILY === 'Windows' && is_dir('D:\\laragon')) {
            $candidates[] = 'D:\\laragon\\etc\\ssl\\cacert.pem';
        }

        foreach ($candidates as $path) {
            if (is_readable($path)) {
                return $path;
            }
        }

        return null;
    }
}
