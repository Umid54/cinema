<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Policies\FavoritePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // модель не нужна, policy логическая
        'favorite' => FavoritePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        /**
         * ❤️ СЧЁТЧИК ИЗБРАННОГО (доступен во всех Blade)
         */
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $view->with(
                    'favoritesCount',
                    auth()->user()
                        ->favorites()
                        ->count()
                );
            }
        });
    }
}
