<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Before launch: hosts from COMING_SOON_HOSTS get the "coming soon" page for every URL.
 */
class ComingSoon
{
    private const EXCEPT = ['admin', 'admin/*', 'robots.txt', 'up', 'build/*', 'css/*', 'favicon.ico'];

    public function handle(Request $request, Closure $next): Response
    {
        $hosts = config('launch.coming_soon_hosts', []);

        if (! in_array(strtolower($request->getHost()), $hosts, true) || $request->is(...self::EXCEPT)) {
            return $next($request);
        }

        $supported = array_keys(config('locales.supported'));
        $locale = $request->segment(1);
        if (! in_array($locale, $supported, true)) {
            $locale = $request->getPreferredLanguage($supported);
        }
        app()->setLocale(in_array($locale, $supported, true) ? $locale : config('locales.default'));

        return response()
            ->view('coming-soon', [
                'whatsapp' => Setting::get('whatsapp'),
                'telegram' => Setting::get('telegram'),
            ])
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('Cache-Control', 'no-store');
    }
}
