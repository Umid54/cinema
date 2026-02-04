<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FreeEpisodeLimit
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->is_premium) {
            return $next($request);
        }

        $key = 'free_episode_limit_' . $user->id;

        if (Cache::has($key)) {
            abort(429, 'Лимит FREE-доступа исчерпан. Оформите Premium.');
        }

        Cache::put($key, true, now()->addDay());

        return $next($request);
    }
}
