@extends('layouts.guest-hud')

@section('title', 'Восстановление пароля')

@section('content')

<div class="hud-card w-full max-w-md p-8 rounded-2xl">

    {{-- HEADER --}}
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold tracking-widest text-cyan-400">
            ВОССТАНОВЛЕНИЕ
        </h1>
        <p class="mt-2 text-sm" style="color:#cbd5f5">
            Укажите email для сброса пароля
        </p>
    </div>

    {{-- Status --}}
    <x-auth-session-status
        class="mb-4 text-sm"
        style="color:#34d399"
        :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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

        {{-- Actions --}}
        <div class="flex justify-between items-center pt-2">
            <a href="{{ route('login') }}"
               class="text-sm"
               style="color:#94a3b8">
                ← Назад к входу
            </a>

            <button type="submit"
                class="px-6 py-2 rounded-xl
                       border border-cyan-400/60
                       text-cyan-300
                       hover:shadow-[0_0_30px_rgba(34,211,238,.6)]
                       transition">
                Отправить ссылку
            </button>
        </div>
    </form>

</div>

@endsection
