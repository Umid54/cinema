<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // 🔥 Новые релизы (фильмы, не сериалы)
        $latestMovies = Movie::published()
            ->where('is_series', false)
            ->latest()
            ->limit(8)
            ->get();

        // ❤️ Счётчик избранного (для навигации)
        $favoritesCount = 0;

        if (Auth::check()) {
            $favoritesCount = Auth::user()
                ->favorites()
                ->count();
        }

        // 📰 Задел под новости / подборки (пока пусто)
        $news = collect();

        return view('home', compact(
            'latestMovies',
            'favoritesCount',
            'news'
        ));
    }
}
