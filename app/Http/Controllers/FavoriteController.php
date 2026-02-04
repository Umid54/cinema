<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    /**
     * 📄 Страница избранного
     */
    public function index(): View
    {
        $favorites = auth()->user()
            ->favorites()
            ->with('favoritable')
            ->latest()
            ->get();

        return view('favorites.index', compact('favorites'));
    }

    /**
     * ❤️ AJAX toggle favorite (Movie)
     */
    public function toggle(Movie $movie): JsonResponse
    {
        $user = auth()->user();

        // Ищем существующий favorite
        $favorite = $user->favorites()
            ->where('favoritable_type', Movie::class)
            ->where('favoritable_id', $movie->id)
            ->first();

        /**
         * ❌ УДАЛЕНИЕ
         */
        if ($favorite) {
            $favorite->delete();

            return response()->json([
                'status' => 'removed',
                'count'  => $user->favorites()->count(),
            ], 200);
        }

        /**
         * 🔐 FREE / PREMIUM лимит
         */
        if (!$user->is_premium) {
            $limit = (int) config('favorites.free_limit', 10);

            $currentCount = $user->favorites()
                ->where('favoritable_type', Movie::class)
                ->count();

            if ($currentCount >= $limit) {
                return response()->json([
                    'status'  => 'limit',
                    'message' => 'Лимит избранного доступен только для Premium',
                    'count'   => $currentCount,
                ], 403);
            }
        }

        /**
         * ✅ ДОБАВЛЕНИЕ
         * firstOrCreate защищает от двойного клика / гонок
         */
        $user->favorites()->firstOrCreate([
            'favoritable_type' => Movie::class,
            'favoritable_id'   => $movie->id,
        ]);

        return response()->json([
            'status' => 'added',
            'count'  => $user->favorites()->count(),
        ], 200);
    }
}
