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
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Movie::query()
            ->published()
            ->movies()
            ->whereNotNull('hls_path')
            ->with('genres')
            ->when($userId, function ($q) use ($userId) {
                $q->withExists([
                    'favorites as is_favorited' => fn ($q) =>
                        $q->where('user_id', $userId)
                ]);
            })
            ->latest();

        if ($request->filled('genre')) {
            $query->whereHas('genres', fn ($q) =>
                $q->where('slug', $request->genre)
            );
        }

        $movies = $query->get()->filter(fn ($movie) =>
            $movie->hls_path
            && Storage::disk('public')->exists($movie->hls_path)
        );

        $movies = $this->paginate($movies);

        $genres = Genre::orderBy('name')->get();

        return view('movies.index', compact('movies', 'genres'));
    }

    /**
     * 🆕 Новинки
     */
    public function new()
    {
        return $this->listing(
            Movie::published()->movies()->latest(),
            'Новинки'
        );
    }

    /**
     * 🔥 Популярные
     */
    public function popular()
    {
        return $this->listing(
            Movie::published()->movies()->popular(),
            'Популярные'
        );
    }

    /**
     * 🎭 По жанру
     */
    public function genre(string $genre)
    {
        return $this->listing(
            Movie::published()->movies()->byGenre($genre),
            'Жанр'
        );
    }

    /**
     * 📅 По году
     */
    public function year(int $year)
    {
        return $this->listing(
            Movie::published()->movies()->byYear($year),
            "Фильмы $year"
        );
    }

    /**
     * ▶️ Просмотр фильма
     */
    public function watch(Movie $movie)
    {
        $user = Auth::user();

        $movie->load([
            'genres',
            'countries',
            'persons',
            'screenshots',
        ]);

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

        $canWatch = $user && $user->is_premium;

        return view('movies.watch', compact('movie', 'canWatch'));
    }

    /* ===================================================== */

    /**
     * 🔁 Общий метод листинга (без дублирования)
     */
    protected function listing($baseQuery, string $title)
    {
        $userId = Auth::id();

        $movies = $baseQuery
            ->whereNotNull('hls_path')
            ->with('genres')
            ->when($userId, function ($q) use ($userId) {
                $q->withExists([
                    'favorites as is_favorited' => fn ($q) =>
                        $q->where('user_id', $userId)
                ]);
            })
            ->get()
            ->filter(fn ($movie) =>
                $movie->hls_path
                && Storage::disk('public')->exists($movie->hls_path)
            );

        $movies = $this->paginate($movies);

        $genres = Genre::orderBy('name')->get();

        return view('movies.index', compact('movies', 'genres', 'title'));
    }

    /**
     * 📄 Ручная пагинация (единая)
     */
    protected function paginate($items, int $perPage = 24)
    {
        $page = request()->get('page', 1);

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
