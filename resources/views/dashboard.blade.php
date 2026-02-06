@extends('layouts.user-hud')

@section('title', 'Личный кабинет')

@section('content')

<h1 class="text-3xl font-bold text-cyan-400 mb-10 tracking-widest">
    ЛИЧНЫЙ КАБИНЕТ
</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">

    {{-- Profile --}}
    <div class="hud-glass hud-glow p-8 rounded-2xl">
        <div class="text-sm text-slate-400 mb-2">
            Профиль
        </div>

        <div class="text-lg font-semibold text-slate-200">
            {{ auth()->user()->name }}
        </div>

        <div class="text-sm text-slate-400 mt-1">
            {{ auth()->user()->email }}
        </div>

        <a href="{{ route('profile.edit') }}"
           class="inline-block mt-6 text-cyan-300 text-sm hover:underline">
            Редактировать профиль →
        </a>
    </div>

    {{-- Access --}}
    <div class="hud-glass hud-glow p-8 rounded-2xl">
        <div class="text-sm text-slate-400 mb-2">
            Доступ
        </div>

        @php
            $user = auth()->user();
            $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
        @endphp

        @if($isAdmin)
            <div class="text-emerald-400 font-semibold">
                Администратор
            </div>

            <a href="{{ route('admin.dashboard') }}"
               class="inline-block mt-6 text-emerald-300 text-sm hover:underline">
                Перейти в админку →
            </a>
        @else
            <div class="text-slate-300">
                Пользователь
            </div>
        @endif
    </div>

    {{-- Status --}}
    <div class="hud-glass hud-glow p-8 rounded-2xl">
        <div class="text-sm text-slate-400 mb-2">
            Статус
        </div>

        @if($user->is_premium_active)
            <div class="text-emerald-400 font-semibold">
                Premium активен
            </div>
        @elseif($user->is_trial)
            <div class="text-cyan-300 font-semibold">
                Пробный период
            </div>
        @else
            <div class="text-slate-300">
                Бесплатный аккаунт
            </div>

            <a href="{{ route('premium.index') }}"
               class="inline-block mt-4 text-cyan-300 text-sm hover:underline">
                Оформить Premium →
            </a>
        @endif
    </div>

</div>

{{-- QUICK LINKS --}}
<div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-6">

    <a href="{{ route('favorites.index') }}"
       class="hud-glass p-6 rounded-xl hover:shadow-[0_0_30px_rgba(248,113,113,.35)] transition">
        ❤️ Избранное
    </a>

    <a href="{{ route('history.index') }}"
       class="hud-glass p-6 rounded-xl hover:shadow-[0_0_30px_rgba(34,211,238,.35)] transition">
        ⏱ История просмотров
    </a>

    <a href="{{ route('premium.index') }}"
       class="hud-glass p-6 rounded-xl hover:shadow-[0_0_30px_rgba(52,211,153,.35)] transition">
        ⭐ Premium
    </a>

</div>

@endsection
