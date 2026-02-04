<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ContentAccess;
use App\Http\Middleware\PremiumMiddleware;
use App\Http\Middleware\FreeEpisodeLimit;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        /**
         * 🔗 Alias middleware (Laravel 12)
         */
        $middleware->alias([
            'admin'       => AdminMiddleware::class,
            'content'     => ContentAccess::class,
            'premium'     => PremiumMiddleware::class,
            'free.limit'  => FreeEpisodeLimit::class,
        ]);

        /**
         * 🛡️ Глобальная защита:
         * - бан
         * - авто-отключение истёкшего trial/premium
         */
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\NormalizePremiumState::class,
            \App\Http\Middleware\BlockBannedUsers::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
