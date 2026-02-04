<form method="POST"
      action="{{ route('admin.movies.store') }}"
      enctype="multipart/form-data"
      class="space-y-6 hud-glass p-8 rounded-2xl">

    @csrf

    <input name="title" placeholder="Название" class="input" required>

    <textarea name="description" placeholder="Описание" class="input"></textarea>

    <input name="year" type="number" placeholder="Год">

    <select name="is_series">
        <option value="0">Фильм</option>
        <option value="1">Сериал</option>
    </select>

    {{-- POSTER --}}
    <div>
        <label class="text-sm text-slate-400">Постер</label>
        <input type="file" name="poster" accept="image/*">
    </div>

    {{-- SCREENSHOTS --}}
    <div>
        <label class="text-sm text-slate-400">Скриншоты</label>
        <input type="file" name="screenshots[]" multiple accept="image/*">
    </div>

    <button class="btn btn-emerald">
        💾 Сохранить
    </button>
</form>
