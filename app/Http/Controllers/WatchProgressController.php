<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WatchProgressService;

class WatchProgressController extends Controller
{
    public function store(Request $request, WatchProgressService $service)
    {
        $user = $request->user();

        // 🔐 Только PREMIUM / TRIAL
        if (! $user || (! $user->is_premium_active && ! $user->is_trial)) {
            return response()->json(['ok' => false], 403);
        }

        $data = $request->validate([
            'series_id'        => ['required', 'integer'],
            'season'           => ['required', 'integer'],
            'episode'          => ['required', 'integer'],
            'position_seconds' => ['required', 'integer', 'min:0'],
        ]);

        $service->save(
            $user,
            $data['series_id'],
            $data['season'],
            $data['episode'],
            $data['position_seconds']
        );

        return response()->json(['ok' => true]);
    }
}
