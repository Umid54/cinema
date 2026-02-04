<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizePremiumState
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // 🔁 Вся логика живёт в модели
            $user->normalizePremium();

            /*
             |--------------------------------------------------------------------------
             | 🔒 HARD BLOCK FOR PREMIUM ROUTES
             |--------------------------------------------------------------------------
             | Если маршрут требует premium, а пользователь не premium —
             | сразу редиректим на страницу апгрейда
             */
            $routeMiddlewares = $request->route()?->gatherMiddleware() ?? [];

            if (
                in_array('premium', $routeMiddlewares, true)
                && !$user->is_premium
            ) {
                return redirect()->route('premium.index');
            }
        }

        return $next($request);
    }
}
