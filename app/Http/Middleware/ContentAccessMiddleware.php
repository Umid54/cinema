<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('status', 'Войдите, чтобы получить доступ к контенту');
        }

        return $next($request);
    }
}
