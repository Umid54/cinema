<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $ip  = $request->ip();
        $now = Carbon::now();

        /**
         * ⛔ Блок регистрации с забаненного IP
         */
        if (
            User::where('register_ip', $ip)
                ->where('is_banned', true)
                ->exists()
        ) {
            abort(403, 'Регистрация с этого IP запрещена');
        }

        /**
         * 🎁 Trial Premium — строго 1 раз на IP (24 часа)
         */
        $trialAlreadyUsed = User::where('register_ip', $ip)
            ->where('trial_used', true)
            ->exists();

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),

            // IP tracking
            'register_ip' => $ip,
            'last_ip'     => $ip,

            // Trial / Premium
            'trial_used'        => !$trialAlreadyUsed,
            'trial_started_at' => !$trialAlreadyUsed ? $now : null,
            'is_premium'       => !$trialAlreadyUsed,
            'premium_until'    => !$trialAlreadyUsed
                ? $now->copy()->addHours(24)
                : null,

            // Security
            'is_banned'   => false,
        ]);

        event(new Registered($user));
        Auth::login($user);

        // ❌ dashboard не используем → сразу в ЛК
        return redirect()->route('account.index');
    }
}
