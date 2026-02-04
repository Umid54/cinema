<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AntiMultiAccount
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) return $next($request);

        $ip = $request->ip();

        // Если пользователь уже забанен
        if ($user->is_banned) {
            abort(403, 'Аккаунт заблокирован');
        }

        // Поиск других аккаунтов с trial с того же IP
        $exists = User::where('id', '!=', $user->id)
            ->where('register_ip', $ip)
            ->where('trial_used', true)
            ->exists();

        if ($exists && $user->trial_used) {
            $user->update(['is_banned' => true]);
            abort(403, 'Мультиаккаунтинг запрещён. Аккаунт заблокирован.');
        }

        // Обновляем IP
        $user->update(['last_ip' => $ip]);

        return $next($request);
    }
}
