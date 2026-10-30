<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CachePublicResponse
{
    /**
     * 公开内容页（GET 且 200）开启公共缓存，降低 TTFB 与源站负载。
     * 表单提交、重定向等非 GET/非 200 响应保持不缓存。
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->method() === 'GET' && $response->getStatusCode() === 200) {
            $response->headers->set(
                'Cache-Control',
                'public, max-age=300, stale-while-revalidate=86400'
            );
            $response->headers->set('Vary', 'Accept-Encoding');
        }

        return $response;
    }
}
