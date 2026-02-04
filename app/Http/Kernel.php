<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     */
    protected $routeMiddleware = [
        /*
        |--------------------------------------------------------------------------
        | Auth
        |--------------------------------------------------------------------------
        */
        'auth'     => \App\Http\Middleware\Authenticate::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        /*
        |--------------------------------------------------------------------------
        | Free / limits
        |--------------------------------------------------------------------------
        */
        // Лимит бесплатных эпизодов (SERIES)
        'free.episode.limit' => \App\Http\Middleware\LimitFreeEpisodes::class,

        // Общий free-лимит (если используется отдельно)
        'free.limit' => \App\Http\Middleware\FreeEpisodeLimit::class,

        /*
        |--------------------------------------------------------------------------
        | Content / Watch
        |--------------------------------------------------------------------------
        */
        // Общая логика доступа к контенту (если применяется точечно)
        'content' => \App\Http\Middleware\ContentAccessMiddleware::class,

        // Подключение прогресса просмотра
        'watch.progress' => \App\Http\Middleware\AttachWatchProgress::class,

        /*
        |--------------------------------------------------------------------------
        | Premium / Admin
        |--------------------------------------------------------------------------
        */
        // 👑 Premium-доступ (КРИТИЧНО для HLS)
        'premium' => \App\Http\Middleware\PremiumMiddleware::class,

        // 🛠 Админка
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ];
}
