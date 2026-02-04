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
           class="inline-block mt-6 text-cyan-300 text-sm
                  hover:underline">
            Редактировать профиль →
        </a>
    </div>

    {{-- Access --}}
    <div class="hud-glass hud-glow p-8 rounded-2xl">
        <div class="text-sm text-slate-400 mb-2">
            Доступ
        </div>

        @if(auth()->user()->isAdmin())
            <div class="text-emerald-400 font-semibold">
                Администратор
            </div>

            <a href="{{ route('admin.dashboard') }}"
               class="inline-block mt-6 text-emerald-300 text-sm
                      hover:underline">
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
        <div class="text-emerald-400 font-semibold">
            Аккаунт активен
        </div>
    </div>

</div>

@endsection
