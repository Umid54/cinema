<?php

namespace App\Http\Controllers;

use App\Models\WatchProgress;
use Illuminate\View\View;

class WatchHistoryController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        abort_unless(
            $user->is_premium_active || $user->is_trial,
            403
        );

        $history = WatchProgress::with('series')
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->get();

        $lastWatched = $history->first();

        return view('history.index', compact('history', 'lastWatched'));
    }
}
