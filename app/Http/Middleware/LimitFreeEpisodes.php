<?php

namespace App\Http\Middleware;

use App\Models\EpisodeView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LimitFreeEpisodes
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 👑 PREMIUM / TRIAL — без ограничений
        if ($user && ($user->is_premium_active || $user->is_trial)) {
            return $next($request);
        }

        $today = now()->toDateString();
        $ip = $request->ip();

        if ($user) {
            // FREE (авторизованный)
            $alreadyViewed = EpisodeView::where('user_id', $user->id)
                ->where('view_date', $today)
                ->exists();
        } else {
            // Гость
            $alreadyViewed = EpisodeView::where('ip', $ip)
                ->where('view_date', $today)
                ->exists();
        }

        // 🔑 КЛЮЧЕВОЕ ИЗМЕНЕНИЕ:
        // НЕ блокируем, а передаём флаг дальше
        $request->attributes->set('free_limit_exceeded', $alreadyViewed);

        return $next($request);
    }
}
