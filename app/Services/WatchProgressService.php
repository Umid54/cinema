<?php

namespace App\Services;

use App\Models\WatchProgress;
use App\Models\User;

class WatchProgressService
{
    /**
     * Получить прогресс просмотра сериала
     * (ТОЛЬКО для Premium / Trial)
     */
    public function getForSeries(User $user, int $seriesId): ?WatchProgress
    {
        if (! $this->canUseProgress($user)) {
            return null;
        }

        return WatchProgress::where('user_id', $user->id)
            ->where('series_id', $seriesId)
            ->first();
    }

    /**
     * Сохранить прогресс просмотра
     */
    public function save(
        User $user,
        int $seriesId,
        int $season,
        int $episode,
        int $positionSeconds
    ): WatchProgress {
        if (! $this->canUseProgress($user)) {
            throw new \RuntimeException('Watch progress is allowed only for premium users.');
        }

        return WatchProgress::updateOrCreate(
            [
                'user_id'   => $user->id,
                'series_id' => $seriesId,
            ],
            [
                'season'           => $season,
                'episode'          => $episode,
                'position_seconds' => max(0, $positionSeconds),
            ]
        );
    }

    /**
     * Проверка доступа к прогрессу
     */
    protected function canUseProgress(User $user): bool
    {
        return $user->is_premium_active || $user->is_trial;
    }
}
