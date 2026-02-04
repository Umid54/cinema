@extends('layout')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center px-6
            bg-gradient-to-b from-[#050b1a] via-[#060d22] to-[#020713]">

    <div class="relative max-w-xl w-full
                rounded-3xl
                border border-cyan-400/20
                bg-gradient-to-br from-[#0a142f]/90 to-[#050a1d]/90
                shadow-[0_0_120px_rgba(34,211,238,0.15)]
                p-12 text-center overflow-hidden">

        {{-- Neon glow --}}
        <div class="absolute inset-0 rounded-3xl
                    bg-[radial-gradient(circle_at_top,rgba(34,211,238,0.18),transparent_70%)]
                    pointer-events-none"></div>

        {{-- Icon --}}
        <div class="relative z-10 text-6xl mb-6">
            🔒
        </div>

        {{-- Title --}}
        <h1 class="relative z-10 text-3xl font-semibold tracking-wide
                   text-cyan-300 mb-4">
            Доступ только для Premium
        </h1>

        {{-- Description --}}
        <p class="relative z-10 text-slate-400 text-sm leading-relaxed mb-10">
            Эта функция доступна только Premium-пользователям.
            Получите полный доступ к фильмам, сериалам и возможностям платформы.
        </p>

        {{-- CTA --}}
        <a href="{{ route('premium.index') }}"
           class="relative z-10 inline-flex items-center justify-center gap-2
                  px-10 py-3 rounded-xl
                  bg-gradient-to-r from-cyan-400 to-emerald-400
                  text-black font-semibold tracking-wide
                  hover:shadow-[0_0_40px_rgba(34,211,238,0.6)]
                  transition">

            👑 Оформить Premium
        </a>

        {{-- Benefits --}}
        <div class="relative z-10 mt-8 text-xs text-slate-500">
            Без рекламы • Без лимитов • Избранное • Прогресс
        </div>
    </div>

</div>
@endsection
