@extends('admin.layout')

@section('title', $movie->exists ? 'Редактирование контента' : 'Добавление контента')
@section('header', $movie->exists ? 'Редактировать фильм / сериал' : 'Добавить фильм / сериал')

@section('content')

<form method="POST"
      enctype="multipart/form-data"
      action="{{ $movie->exists ? route('admin.movies.update', $movie) : route('admin.movies.store') }}"
      class="space-y-10 admin-form">

    @csrf
    @if($movie->exists)
        @method('PUT')
    @endif

    {{-- ОСНОВНАЯ ИНФОРМАЦИЯ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ЛЕВАЯ ЧАСТЬ --}}
        <div class="lg:col-span-2 space-y-6">

            <div>
                <label class="hud-muted text-xs">Название</label>
                <input type="text" name="title"
                       value="{{ old('title', $movie->title) }}"
                       class="w-full mt-1 px-4 py-3 rounded-xl">
            </div>

            <div>
                <label class="hud-muted text-xs">Оригинальное название</label>
                <input type="text" name="original_title"
                       value="{{ old('original_title', $movie->original_title) }}"
                       class="w-full mt-1 px-4 py-3 rounded-xl">
            </div>

            <div>
                <label class="hud-muted text-xs">Описание</label>
                <textarea name="description" rows="5"
                          class="w-full mt-1 px-4 py-3 rounded-xl">{{ old('description', $movie->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                <div>
                    <label class="hud-muted text-xs">Год</label>
                    <input type="number" name="year"
                           value="{{ old('year', $movie->year) }}"
                           class="w-full mt-1 px-3 py-2 rounded-xl">
                </div>

                <div>
                    <label class="hud-muted text-xs">Длительность (в минутах)</label>
                    <input type="number" name="duration"
                           value="{{ old('duration', $movie->duration) }}"
                           class="w-full mt-1 px-3 py-2 rounded-xl">
                </div>

                <div>
                    <label class="hud-muted text-xs">Рейтинг</label>
                    <input type="number" step="0.1" name="rating"
                           value="{{ old('rating', $movie->rating) }}"
                           class="w-full mt-1 px-3 py-2 rounded-xl">
                </div>

                <div>
                    <label class="hud-muted text-xs">Статус</label>
                    <select name="status"
                            class="w-full mt-1 px-3 py-2 rounded-xl">
                        <option value="processing"
                            @selected(old('status', $movie->status) === 'processing')>
                            Обработка
                        </option>
                        <option value="ready"
                            @selected(old('status', $movie->status) === 'ready')>
                            Готово (показывается на сайте)
                        </option>
                        <option value="error"
                            @selected(old('status', $movie->status) === 'error')>
                            Ошибка
                        </option>
                    </select>
                </div>

            </div>

            <div class="flex items-center gap-3 pt-2">
                <input type="checkbox" name="is_series" value="1"
                       @checked(old('is_series', $movie->is_series))>
                <span class="hud-muted text-sm">Это сериал</span>
            </div>

            {{-- SOURCE VIDEO --}}
            <div>
                <label class="hud-muted text-xs mb-2 block">
                    Исходное видео (любой формат)
                </label>

                <input type="file"
                       name="source_video"
                       accept="video/*">

                <p class="text-xs text-slate-400 mt-2">
                    Поддерживаются все видеоформаты (MP4, MKV, AVI, MOV, WEBM и др.).
                    После загрузки видео автоматически конвертируется в HLS
                    (1080p / 720p / 480p) и появляется на сайте.
                </p>
            </div>

            {{-- HLS PATH --}}
            @if($movie->exists)
                <div>
                    <label class="hud-muted text-xs">HLS путь (автоматически)</label>
                    <input type="text"
                           value="{{ $movie->hls_path }}"
                           class="w-full mt-1 px-3 py-2 rounded-xl opacity-60"
                           disabled>
                </div>
            @endif

        </div>

        {{-- ПРАВАЯ ЧАСТЬ --}}
        <div class="space-y-6">

            {{-- ПОСТЕР --}}
            <div>
                <label class="hud-muted text-xs mb-2 block">Постер</label>

                @if($movie->poster_url)
                    <img src="{{ $movie->poster_url }}"
                         class="rounded-xl mb-3 border border-neutral-700">
                @endif

                <input type="file" name="poster">
            </div>

            {{-- СКРИНШОТЫ --}}
            <div>
                <label class="hud-muted text-xs mb-2 block">Скриншоты</label>
                <input type="file" name="screenshots[]" multiple>
            </div>

        </div>
    </div>

    {{-- ЖАНРЫ --}}
    <div>
        <label class="hud-muted text-xs mb-3 block">Жанры</label>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach($genres as $genre)
                <label class="flex items-center gap-2 text-sm hud-text">
                    <input type="checkbox"
                           name="genres[]"
                           value="{{ $genre->id }}"
                           @checked($movie->genres->contains($genre))>
                    {{ $genre->name }}
                </label>
            @endforeach
        </div>
    </div>

    {{-- КНОПКИ --}}
    <div class="flex justify-end gap-4 pt-6">
        <a href="{{ route('admin.movies.index') }}"
           class="btn btn-ghost">
            Назад
        </a>

        <button type="submit"
                class="btn btn-emerald">
            💾 Сохранить
        </button>
    </div>

</form>

@endsection
