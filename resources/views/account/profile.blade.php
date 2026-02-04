@extends('layouts.user-hud')

@section('title', 'Личный кабинет')

@section('content')
<div class="grid grid-cols-12 gap-10">

    <aside class="col-span-3 hud-glass p-6 rounded-2xl">
        <div class="mb-6">
            <div class="text-sm text-slate-400">Аккаунт</div>
            <div class="text-lg font-semibold text-slate-100">
                {{ $user->email }}
            </div>
        </div>

        <nav class="space-y-4 text-sm">
            <a href="{{ route('account.profile') }}" class="block text-cyan-400">
                👤 Профиль
            </a>

            <a href="{{ route('favorites.index') }}"
               class="block text-slate-300 hover:text-cyan-300 transition">
                ❤️ Избранное
            </a>

            <span class="block text-slate-500 cursor-not-allowed">
                👑 Premium (скоро)
            </span>
        </nav>
    </aside>

    <section class="col-span-9">
        <div class="hud-glass p-8 rounded-2xl max-w-xl">
            <h1 class="text-xl font-semibold mb-6">👤 Профиль</h1>

            @if(session('success'))
                <div class="mb-4 text-emerald-400">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="text-sm text-slate-400">Имя</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        class="w-full mt-1 bg-black/40 border border-slate-700 rounded-xl px-4 py-2"
                        required
                    >
                </div>

                <div>
                    <label class="text-sm text-slate-400">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        class="w-full mt-1 bg-black/40 border border-slate-700 rounded-xl px-4 py-2"
                        required
                    >
                </div>

                <button
                    class="px-6 py-2 rounded-xl border border-cyan-400/60 text-cyan-300 hover:bg-cyan-400/10 transition">
                    Сохранить
                </button>
            </form>
        </div>
    </section>

</div>
@endsection

