<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PremiumMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Неавторизован
        |--------------------------------------------------------------------------
        */
        if (!$user) {
            return $this->deny($request, 'Требуется авторизация');
        }

        /*
        |--------------------------------------------------------------------------
        | Администратор — всегда разрешено
        |--------------------------------------------------------------------------
        */
        if (
            (method_exists($user, 'hasRole') && $user->hasRole('admin'))
            || !empty($user->is_admin)
        ) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Авто-нормализация Premium / Trial
        |--------------------------------------------------------------------------
        */
        if (method_exists($user, 'normalizePremium')) {
            $user->normalizePremium();
            $user->refresh();
        }

        /*
        |--------------------------------------------------------------------------
        | Активен Premium ИЛИ Trial
        |--------------------------------------------------------------------------
        */
        if (
            $user->is_premium_active === true
            || $user->is_trial === true
        ) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Нет доступа
        |--------------------------------------------------------------------------
        */
        return $this->deny(
            $request,
            'Доступ доступен только Premium-пользователям'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Deny helper
    |--------------------------------------------------------------------------
    */
    protected function deny(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message'           => $message,
                'premium_required'  => true,
            ], 403);
        }

        return response()->view('errors.premium', [
            'message' => $message,
        ], 403);
    }
}
