@extends('layouts.user-hud')

@section('title', 'Тренды избранного')

@section('content')

<h1 class="text-3xl font-bold text-cyan-400 mb-10 tracking-widest">
    📈 ТРЕНДЫ ИЗБРАННОГО
</h1>

{{-- 7 DAYS --}}
<h2 class="text-xl font-semibold text-emerald-400 mb-4">
    🚀 Рост за 7 дней
</h2>

@include('admin.analytics._trends-table', ['items' => $trends7])

{{-- 30 DAYS --}}
<h2 class="text-xl font-semibold text-sky-400 mt-12 mb-4">
    📊 Рост за 30 дней
</h2>

@include('admin.analytics._trends-table', ['items' => $trends30])

@endsection
