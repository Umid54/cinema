<?php

namespace App\Policies;

use App\Models\User;

class FavoritePolicy
{
    /**
     * Можно ли добавить в избранное
     */
    public function create(User $user): bool
    {
        // Premium — без лимита
        if ($user->is_premium) {
            return true;
        }

        // Free — проверяем лимит
        $limit = config('favorites.free_limit');

        return $user->favorites()->count() < $limit;
    }
}
