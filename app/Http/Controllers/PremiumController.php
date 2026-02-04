<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PremiumController extends Controller
{
    /**
     * Страница Premium (PUBLIC)
     */
    public function index(): View
    {
        return view('premium.index', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * POST: активация TRIAL (24 часа)
     */
    public function startTrial(Request $request): RedirectResponse
    {
        $user = $request->user();

        // 🛑 Если уже Premium — trial не нужен
        if ($user->account_status === 'PREMIUM') {
            return redirect()
                ->route('premium.index')
                ->with('flash', [
                    'type'    => 'info',
                    'message' => 'У вас уже активирован Premium 👑',
                ]);
        }

        // 🛑 Trial уже использован
        if ($user->trial_used) {
            return redirect()
                ->route('premium.index')
                ->with('flash', [
                    'type'    => 'warning',
                    'message' => 'Пробный доступ уже был использован',
                ]);
        }

        // ✅ Активация Trial
        $user->update([
            'trial_used'       => true,
            'trial_started_at' => now(),
        ]);

        return redirect()
            ->route('premium.index')
            ->with('flash', [
                'type'    => 'success',
                'message' => 'Пробный доступ активирован на 24 часа 🎁',
            ]);
    }

    /**
     * POST: активация Premium (платежи — позже)
     */
    public function activate(Request $request): RedirectResponse
    {
        $user = $request->user();

        // 🛑 Уже Premium
        if ($user->account_status === 'PREMIUM') {
            return redirect()
                ->route('account.index')
                ->with('flash', [
                    'type'    => 'info',
                    'message' => 'Premium уже активен 👑',
                ]);
        }

        // ✅ Активация Premium
        $user->update([
            'is_premium'    => true,
            'premium_until' => null, // lifetime по умолчанию
        ]);

        return redirect()
            ->route('account.index')
            ->with('flash', [
                'type'    => 'success',
                'message' => 'Premium успешно активирован 👑',
            ]);
    }
}
