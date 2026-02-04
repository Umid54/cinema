<?php

namespace App\Http\Middleware;

use App\Services\WatchProgressService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AttachWatchProgress
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            auth()->check() &&
            $request->route()?->parameter('series')
        ) {
            $series = $request->route('series');

            $progress = app(WatchProgressService::class)
                ->getForSeries($series->id);

            view()->share('watchProgress', $progress);
        }

        return $next($request);
    }
}
