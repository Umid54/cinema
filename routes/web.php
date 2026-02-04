<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController as FrontMovieController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\SeriesWatchController;
use App\Http\Controllers\SeriesStreamController;
use App\Http\Controllers\MovieStreamController;
use App\Http\Controllers\WatchProgressController;
use App\Http\Controllers\WatchHistoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PremiumController;

use App\Http\Controllers\Admin\MovieController as AdminMovieController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Public pages
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/movies', [FrontMovieController::class, 'index'])
    ->name('movies.index');

Route::get('/series', [SeriesController::class, 'index'])
    ->name('series.index');

/*
|--------------------------------------------------------------------------
| 🎬 Movie watch page (PUBLIC — metadata only)
|--------------------------------------------------------------------------
*/
Route::get('/watch/{movie}', [FrontMovieController::class, 'watch'])
    ->whereNumber('movie')
    ->name('movies.watch');

/*
|--------------------------------------------------------------------------
| 📡 HLS STREAM — MOVIES
| ❗ БЕЗ auth/premium middleware
| ❗ Проверка доступа ТОЛЬКО в MovieStreamController
|--------------------------------------------------------------------------
*/
Route::get(
    '/stream/movies/{movie}/{file}',
    [MovieStreamController::class, 'stream']
)
    ->whereNumber('movie')
    ->where('file', '.*')
    ->name('movies.stream');

/*
|--------------------------------------------------------------------------
| ▶️ Series watch page (FREE LIMIT)
|--------------------------------------------------------------------------
*/
Route::middleware(['free.episode.limit'])->group(function () {
    Route::get(
        '/series/{series}/watch/{season}/{episode}',
        [SeriesWatchController::class, 'watch']
    )
        ->whereNumber('series')
        ->whereNumber('season')
        ->whereNumber('episode')
        ->name('series.watch');
});

/*
|--------------------------------------------------------------------------
| 📡 HLS STREAM — SERIES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get(
        '/stream/series/{series}/{season}/{episode}/{file}',
        [SeriesStreamController::class, 'handle']
    )
        ->whereNumber('series')
        ->whereNumber('season')
        ->whereNumber('episode')
        ->where('file', '.*')
        ->name('series.stream');
});

/*
|--------------------------------------------------------------------------
| ▶️ Episode view tracking
|--------------------------------------------------------------------------
*/
Route::post('/episodes/{episode}/watch', function (
    Request $request,
    \App\Models\Episode $episode
) {
    \App\Models\EpisodeView::create([
        'user_id'    => $request->user()?->id,
        'ip'         => $request->ip(),
        'episode_id' => $episode->id,
        'view_date'  => now()->toDateString(),
    ]);

    return response()->json(['ok' => true]);
})->middleware('free.episode.limit');

/*
|--------------------------------------------------------------------------
| 👑 PREMIUM / AUTH
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'premium'])->group(function () {

    Route::post('/series/progress/save', [WatchProgressController::class, 'store'])
        ->name('series.progress.save');

    Route::get('/history', [WatchHistoryController::class, 'index'])
        ->name('history.index');

    Route::post('/favorites/movie/{movie}', [FavoriteController::class, 'toggle'])
        ->whereNumber('movie')
        ->name('favorites.toggle');

    Route::get('/favorites', [FavoriteController::class, 'index'])
        ->name('favorites.index');
});

/*
|--------------------------------------------------------------------------
| 👑 Premium page
|--------------------------------------------------------------------------
*/
Route::get('/premium', [PremiumController::class, 'index'])
    ->name('premium.index');

/*
|--------------------------------------------------------------------------
| 👤 Account
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    });

    Route::post('/trial/start', [PremiumController::class, 'startTrial'])
        ->name('trial.start');

    Route::post('/premium/activate', [PremiumController::class, 'activate'])
        ->name('premium.activate');
});

/*
|--------------------------------------------------------------------------
| 🛠 Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/analytics/favorites', [AdminAnalyticsController::class, 'favorites'])
            ->name('analytics.favorites');

        Route::get('/analytics/trends', [AdminAnalyticsController::class, 'trends'])
            ->name('analytics.trends');

        Route::resource('movies', AdminMovieController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });

/*
|--------------------------------------------------------------------------
| Auth (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
