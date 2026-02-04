@extends('layouts.guest-hud')

@section('title', 'Подтверждение пароля')

@section('content')

<div class="hud-card w-full max-w-md p-8 rounded-2xl">

    {{-- HEADER --}}
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold tracking-widest text-amber-400">
            ПОДТВЕРЖДЕНИЕ
        </h1>
        <p class="mt-2 text-sm" style="color:#cbd5f5">
            Для продолжения введите ваш пароль
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf

        {{-- Password --}}
        <div>
            <label for="password" class="hud-label">Пароль</label>
            <input
                id="password"
                type="password"
                name="password"
                required autofocus
                class="hud-input w-full mt-2 px-4 py-2 rounded-lg"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-400"/>
        </div>

        {{-- Actions --}}
        <div class="flex justify-between items-center pt-2">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm"
                   style="color:#94a3b8">
                    Забыли пароль?
                </a>
            @endif

            <button type="submit"
                class="px-6 py-2 rounded-xl
                       border border-amber-400/60
                       text-amber-300
                       hover:shadow-[0_0_30px_rgba(251,191,36,.6)]
                       transition">
                Подтвердить
            </button>
        </div>
    </form>

</div>

@endsection
