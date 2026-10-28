<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreUtm
{
    public const KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];

    public function handle(Request $request, Closure $next): Response
    {
        $utm = [];
        foreach (self::KEYS as $key) {
            $value = $request->query($key);
            if (is_string($value) && filled($value)) {
                $utm[$key] = mb_substr(trim($value), 0, 150);
            }
        }

        if ($utm !== [] && $request->hasSession()) {
            $request->session()->put('utm', $utm);
        }

        return $next($request);
    }
}
