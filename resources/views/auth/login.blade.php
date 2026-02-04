@extends('layouts.guest-hud')

@section('title', 'Вход')

@section('content')

<div class="hud-card w-full max-w-md p-8 rounded-2xl">

    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold tracking-widest text-cyan-400">
            ВХОД
        </h1>
        <p class="mt-2 text-sm" style="color:#cbd5f5">
            Авторизация пользователя
        </p>
    </div>

    <x-auth-session-status
        class="mb-4 text-sm"
        style="color:#34d399"
        :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="hud-label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required autofocus
                class="hud-input w-full mt-2 px-4 py-2 rounded-lg"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-400"/>
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="hud-label">Пароль</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                class="hud-input w-full mt-2 px-4 py-2 rounded-lg"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-400"/>
        </div>

        {{-- Remember --}}
        <div class="flex items-center gap-2">
            <input id="remember" type="checkbox" name="remember">
            <label for="remember" class="text-sm" style="color:#cbd5f5">
                Запомнить меня
            </label>
        </div>

        <div class="flex justify-between items-center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm"
                   style="color:#94a3b8">
                    Забыли пароль?
                </a>
            @endif

            <button type="submit"
                class="px-6 py-2 rounded-xl
                       border border-cyan-400/60
                       text-cyan-300
                       hover:shadow-[0_0_30px_rgba(34,211,238,.6)]
                       transition">
                Войти
            </button>
        </div>
    </form>

    {{-- REGISTRATION LINK --}}
    @if (Route::has('register'))
        <div class="mt-8 text-center text-sm" style="color:#cbd5f5">
            Нет аккаунта?
            <a href="{{ route('register') }}"
               class="ml-1 text-emerald-400 hover:underline font-semibold">
                Регистрация
            </a>
        </div>
    @endif

</div>

@endsection
