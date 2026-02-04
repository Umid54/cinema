<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Movie;
use App\Observers\MovieObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Observers
         */
        Movie::observe(MovieObserver::class);

        /**
         * GLOBAL View Composer
         * Счётчик избранного доступен во ВСЕХ layout'ах и страницах
         */
        View::composer('*', function ($view) {
            $view->with(
                'favoritesCount',
                auth()->check()
                    ? auth()->user()->favorites()->count()
                    : 0
            );
        });
    }
}
