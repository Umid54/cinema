@extends('layouts.user-hud')

@section('title', 'Профиль')

@section('content')
<div class="max-w-5xl mx-auto space-y-10 profile-dark">

    {{-- HEADER --}}
    <div class="hud-glass hud-glow rounded-2xl p-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-cyan-400 tracking-widest">
                Профиль
            </h1>
            <div class="text-slate-400 mt-1">
                {{ auth()->user()->email }}
            </div>
        </div>

        @if(auth()->user()->is_premium ?? false)
            <span class="px-4 py-1 rounded-full text-sm
                         border border-emerald-400/50 text-emerald-300">
                👑 Premium
            </span>
        @else
            <span class="px-4 py-1 rounded-full text-sm
                         border border-slate-500/50 text-slate-300">
                Free
            </span>
        @endif
    </div>

    {{-- UPDATE PROFILE --}}
    <div class="hud-glass hud-glow rounded-2xl p-8">
        <h2 class="text-lg font-semibold text-slate-200 mb-6">
            👤 Данные профиля
        </h2>

        <div class="max-w-xl space-y-4">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- UPDATE PASSWORD --}}
    <div class="hud-glass hud-glow rounded-2xl p-8">
        <h2 class="text-lg font-semibold text-slate-200 mb-6">
            🔐 Смена пароля
        </h2>

        <div class="max-w-xl space-y-4">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    {{-- DELETE ACCOUNT --}}
    <div class="hud-glass rounded-2xl p-8 border border-rose-500/30">
        <h2 class="text-lg font-semibold text-rose-400 mb-6">
            ⚠️ Удаление аккаунта
        </h2>

        <div class="max-w-xl space-y-4">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection
