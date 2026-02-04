@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-24 text-center" data-spotlight>
    <h1 class="text-2xl font-bold text-white mb-4">
        Лимит исчерпан
    </h1>

    <p class="text-neutral-400 mb-6">
        Бесплатно доступна <b>1 серия в сутки</b>.<br>
        Оформите Premium, чтобы смотреть без ограничений.
    </p>

    <a href="{{ route('premium.index') }}"
       class="btn btn-emerald">
        Оформить Premium
    </a>
</div>
@endsection
