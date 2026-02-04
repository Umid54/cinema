<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // пока просто пускаем всех авторизованных
        return $next($request);
    }
}
