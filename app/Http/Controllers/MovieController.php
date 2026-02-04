<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class MovieController extends Controller
{
    /**
     * 🎬 Каталог фильмов
     * Показываем ТОЛЬКО реально готовые:
     *  - status = ready
     *  - hls_path существует
     *  - файл master.m3u8 реально есть на диске
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Movie::query()
            ->where('status', 'ready')
            ->whereNotNull('hls_path')
            ->with('genres')
            ->when($userId, function ($q) use ($userId) {
                $q->withExists([
                    'favorites as is_favorited' => fn ($q) =>
                        $q->where('user_id', $userId)
                ]);
            })
            ->latest();

        // 🎯 Фильтр по жанру
        if ($request->filled('genre')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('slug', $request->genre);
            });
        }

        // 🔥 Фильтрация по реальному существованию HLS
        $movies = $query->get()->filter(function ($movie) {
            return $movie->hls_path
                && Storage::disk('public')->exists($movie->hls_path);
        });

        // 📄 Ручная пагинация
        $page    = request()->get('page', 1);
        $perPage = 24;

        $movies = new LengthAwarePaginator(
            $movies->forPage($page, $perPage)->values(),
            $movies->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $genres = Genre::orderBy('name')->get();

        return view('movies.index', compact('movies', 'genres'));
    }

    /**
     * ▶️ Просмотр фильма
     * /watch/{movie}
     *
     * 🔐 Доступ:
     *  - guest → middleware auth
     *  - user → страница
     *  - premium → видео
     */
    public function watch(Movie $movie)
    {
        $user = Auth::user(); // guest сюда уже не попадёт (auth middleware)

        /* ================== DATA ================== */
        $movie->load([
            'genres',
            'countries',
            'persons',
            'screenshots',
        ]);

        /* ================== AUTO FIX ================== */
        if (
            $movie->status === 'ready'
            && (
                !$movie->hls_path
                || !Storage::disk('public')->exists($movie->hls_path)
            )
        ) {
            $movie->update([
                'status'   => 'draft',
                'hls_path' => null,
            ]);
        }

        /* ================== ACCESS ================== */
        $canWatch = false;

        if ($user && $user->is_premium) {
            $canWatch = true;
        }

        return view('movies.watch', compact('movie', 'canWatch'));
    }
}
