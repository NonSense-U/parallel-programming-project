<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogFpmUpstream
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $fpmUpstream = $request->header('X-FPM-Upstream');
        $fpmHostname = gethostname() ?: null;

        $shouldLog = config('app.debug')
            || $request->boolean('log_fpm')
            || $request->headers->has('X-Log-Fpm');

        if ($fpmUpstream !== null && $fpmUpstream !== '') {
            Log::withContext(['fpm_upstream' => $fpmUpstream]);
        }

        if ($fpmHostname !== null && $fpmHostname !== '') {
            Log::withContext(['fpm_hostname' => $fpmHostname]);
        }

        $response = $next($request);

        if ($shouldLog) {
            Log::info('Request handled by php-fpm', [
                'method' => $request->method(),
                'path' => $request->path(),
                'fpm_hostname' => $fpmHostname,
                'fpm_upstream' => $fpmUpstream,
            ]);
        }

        return $response;
    }
}
