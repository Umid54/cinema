@extends('layouts.guest-hud')

@section('title', 'Сброс пароля')

@section('content')

<div class="hud-card w-full max-w-md p-8 rounded-2xl">

    {{-- HEADER --}}
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold tracking-widest text-cyan-400">
            СБРОС ПАРОЛЯ
        </h1>
        <p class="mt-2 text-sm" style="color:#cbd5f5">
            Установите новый пароль
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
        @csrf

        {{-- Token --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div>
            <label for="email" class="hud-label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                required autofocus
                class="hud-input w-full mt-2 px-4 py-2 rounded-lg"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-400"/>
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="hud-label">Новый пароль</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                class="hud-input w-full mt-2 px-4 py-2 rounded-lg"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-400"/>
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="hud-label">
                Подтверждение пароля
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                class="hud-input w-full mt-2 px-4 py-2 rounded-lg"
            >
        </div>

        {{-- Actions --}}
        <div class="flex justify-between items-center pt-2">
            <a href="{{ route('login') }}"
               class="text-sm"
               style="color:#94a3b8">
                ← К входу
            </a>

            <button type="submit"
                class="px-6 py-2 rounded-xl
                       border border-cyan-400/60
                       text-cyan-300
                       hover:shadow-[0_0_30px_rgba(34,211,238,.6)]
                       transition">
                Сохранить пароль
            </button>
        </div>
    </form>

</div>

@endsection
