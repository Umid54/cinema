@extends('layouts.user-hud')

@section('title', 'Premium')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-10">

    <div data-spotlight
         class="hud-glass rounded-2xl p-8 text-center">

        <h1 class="text-3xl font-bold text-cyan-300 mb-4">
            👑 Premium-доступ
        </h1>

        <p class="text-slate-300 mb-8">
            Максимальный опыт просмотра без ограничений и блокировок
        </p>

        <ul class="text-sm text-slate-400 mb-10 space-y-2 text-left max-w-md mx-auto">
            <li>✔ Без лимитов на серии</li>
            <li>✔ Resume и история просмотров</li>
            <li>✔ Все качества до 1080p</li>
            <li>✔ Доступ ко всему контенту</li>
        </ul>

        {{-- GUEST --}}
        @guest
            <a href="{{ route('login') }}"
               class="btn btn-sky w-full">
                Войти и получить доступ
            </a>
        @endguest

        {{-- AUTH --}}
        @auth

            {{-- PREMIUM --}}
            @if($user->account_status === 'PREMIUM')
                <div class="text-emerald-400 font-medium mb-6">
                    Premium уже активен ✅
                </div>

                <a href="{{ route('account.index') }}"
                   class="inline-block text-sm text-cyan-400 hover:underline">
                    ← Перейти в личный кабинет
                </a>

            {{-- TRIAL --}}
            @elseif($user->account_status === 'TRIAL')
                <div class="mb-6 text-amber-300">
                    ⏳ Активен пробный доступ (24 часа)
                </div>

                <form method="POST" action="{{ route('premium.activate') }}">
                    @csrf
                    <button class="btn btn-emerald w-full">
                        Оформить Premium
                    </button>
                </form>

            {{-- FREE --}}
            @else
                <div class="flex flex-col gap-4">

                    @if(!$user->trial_used)
                        <form method="POST" action="{{ route('trial.start') }}">
                            @csrf
                            <button class="btn btn-sky w-full">
                                🎁 Активировать Trial на 24 часа
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('premium.activate') }}">
                        @csrf
                        <button class="btn btn-emerald w-full">
                            👑 Активировать Premium
                        </button>
                    </form>

                </div>
            @endif

        @endauth

    </div>

    <div class="mt-8 text-center text-xs text-slate-500">
        Premium — это лучший UX и поддержка проекта
    </div>

</div>
@endsection
