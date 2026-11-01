<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class VisitRecorder
{
    private const BOT_PATTERN = '/bot|crawl|spider|slurp|bingpreview|headless|phantom|curl|wget|python-requests|go-http|facebookexternalhit|preview|monitor|uptime/i';

    public function record(Request $request, string $path): bool
    {
        $agent = (string) $request->userAgent();

        if ($agent === '' || preg_match(self::BOT_PATTERN, $agent)) {
            return false;
        }

        $path = $this->normalize($path);

        if ($path === null) {
            return false;
        }

        $day = now()->toDateString();
        $visitor = substr(hash_hmac('sha256', $request->ip().'|'.$agent, (string) config('app.key')), 0, 32);

        try {
            DB::table('page_views')->upsert(
                [['viewed_on' => $day, 'path' => $path, 'hits' => 1]],
                ['viewed_on', 'path'],
                ['hits' => DB::raw('hits + 1')]
            );

            DB::table('visitors')->insertOrIgnore([
                ['viewed_on' => $day, 'visitor_hash' => $visitor],
            ]);

            return true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    private function normalize(string $path): ?string
    {
        $path = trim($path);

        if ($path === '' || ! str_starts_with($path, '/')) {
            return null;
        }

        $path = (string) preg_replace('/[?#].*$/', '', $path);

        if (preg_match('/[\x00-\x1F\x7F]/', $path)) {
            return null;
        }

        if (str_starts_with($path, '/admin') || str_starts_with($path, '/api') || str_starts_with($path, '/_')) {
            return null;
        }

        if (mb_strlen($path) > 180) {
            return null;
        }

        return $path === '/' ? $path : rtrim($path, '/');
    }
}
