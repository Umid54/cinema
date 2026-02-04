@extends('layouts.user-hud')

@section('title', 'Личный кабинет')

@section('content')
@php
    /** @var \App\Models\User $user */
    $user = auth()->user();
    $status = $user->premiumStatus();

    $validUntil = null;
    $lifetime = false;

    if ($user->premium_until) {
        $validUntil = $user->premium_until;
    } elseif ($user->trial_started_at) {
        $validUntil = $user->trial_started_at->copy()->addHours(24);
    } elseif ($status === 'PREMIUM') {
        $lifetime = true;
    }
@endphp

<div class="max-w-4xl mx-auto hud-glass p-8 rounded-2xl">

    <h1 class="text-xl font-semibold mb-8">Личный кабинет</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

        {{-- 👤 ПРОФИЛЬ --}}
        <div class="p-6 rounded-xl border border-cyan-400/60 bg-cyan-400/5">
            <div class="text-lg font-medium mb-4">👤 Профиль</div>

            <div class="space-y-2 text-sm">

                <div>
                    <span class="text-slate-400">Email:</span>
                    <span class="ml-2">{{ $user->email }}</span>
                </div>

                <div>
                    <span class="text-slate-400">Имя:</span>
                    <span class="ml-2">{{ $user->name ?? '—' }}</span>
                </div>

                {{-- STATUS --}}
                <div>
                    <span class="text-slate-400">Статус аккаунта:</span>

                    @if($status === 'PREMIUM')
                        <span class="ml-2 text-emerald-400 font-semibold">
                            👑 PREMIUM
                        </span>
                    @elseif($status === 'TRIAL')
                        <span class="ml-2 text-sky-400 font-semibold">
                            ⏳ TRIAL
                        </span>
                    @else
                        <span class="ml-2 text-slate-300">
                            FREE
                        </span>
                    @endif
                </div>

                {{-- ⏱ Срок действия --}}
                @if($status !== 'FREE')
                    <div>
                        <span class="text-slate-400">Действует до:</span>

                        <span class="ml-2 text-slate-200">
                            @if($validUntil)
                                {{ $validUntil->format('d.m.Y H:i') }}
                            @elseif($lifetime)
                                <span class="text-emerald-400">Бессрочно</span>
                            @else
                                —
                            @endif
                        </span>
                    </div>
                @endif

            </div>

            {{-- 👑 CTA --}}
            @if($status === 'FREE')
                <a href="{{ route('premium.index') }}"
                   class="inline-block mt-5 px-6 py-2 rounded-xl
                          border border-amber-400/50 text-amber-300
                          hover:bg-amber-400/10 transition">
                    👑 Оформить Premium
                </a>
            @endif
        </div>

        {{-- ❤️ ИЗБРАННОЕ --}}
        <a href="{{ route('favorites.index') }}"
           class="block p-6 rounded-xl border border-slate-700
                  hover:border-rose-400/60 hover:bg-rose-400/5 transition">
            <div class="text-lg font-medium mb-1">❤️ Избранное</div>
            <div class="text-sm text-slate-400">
                Сохранённые фильмы
            </div>
        </a>

    </div>
</div>
@endsection
