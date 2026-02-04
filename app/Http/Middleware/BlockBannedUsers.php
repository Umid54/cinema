<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockBannedUsers
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->is_banned) {
            abort(403, 'Аккаунт заблокирован');
        }

        return $next($request);
    }
}
