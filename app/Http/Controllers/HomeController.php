<?php

namespace App\Http\Controllers;

use App\Models\Movie;

class HomeController extends Controller
{
    public function index()
    {
        $latestMovies = Movie::published()
            ->where('is_series', false)
            ->latest()
            ->limit(8)
            ->get();

        return view('home', compact('latestMovies'));
    }
}
