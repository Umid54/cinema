<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Country;
use App\Jobs\ConvertMovieToHls;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::with('screenshots')
            ->latest()
            ->paginate(20);

        return view('admin.movies.index', compact('movies'));
    }

    public function create()
    {
        return view('admin.movies.form', [
            'movie'     => new Movie(),
            'genres'    => Genre::orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['slug'] = $this->uniqueSlug($data['title']);

        if ($request->hasFile('poster')) {
            $data['poster_path'] = $request->file('poster')
                ->store('movies/posters', 'public');
        }

        $movie = Movie::create($data);

        $movie->genres()->sync($request->input('genres', []));
        $movie->countries()->sync($request->input('countries', []));

        if ($request->hasFile('screenshots')) {
            foreach ($request->file('screenshots') as $file) {
                $movie->screenshots()->create([
                    'path' => $file->store('movies/screenshots', 'public'),
                ]);
            }
        }

        if ($request->hasFile('source_video')) {
            $this->handleVideoUpload($request, $movie);
        }

        return redirect()
            ->route('admin.movies.index')
            ->with('success', 'Контент добавлен');
    }

    public function edit(Movie $movie)
    {
        $movie->load('screenshots');

        return view('admin.movies.form', [
            'movie'     => $movie,
            'genres'    => Genre::orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Movie $movie)
    {
        $data = $this->validated($request, $movie->id);

        $data['slug'] = $this->uniqueSlug($data['title'], $movie->id);

        if ($request->hasFile('poster')) {
            if ($movie->poster_path) {
                Storage::disk('public')->delete($movie->poster_path);
            }

            $data['poster_path'] = $request->file('poster')
                ->store('movies/posters', 'public');
        }

        $movie->update($data);

        $movie->genres()->sync($request->input('genres', []));
        $movie->countries()->sync($request->input('countries', []));

        if ($request->hasFile('screenshots')) {
            foreach ($request->file('screenshots') as $file) {
                $movie->screenshots()->create([
                    'path' => $file->store('movies/screenshots', 'public'),
                ]);
            }
        }

        if ($request->hasFile('source_video')) {
            $this->handleVideoUpload($request, $movie);
        }

        return redirect()
            ->route('admin.movies.index')
            ->with('success', 'Контент обновлён');
    }

    public function destroy(Movie $movie)
    {
        if ($movie->poster_path) {
            Storage::disk('public')->delete($movie->poster_path);
        }

        foreach ($movie->screenshots as $shot) {
            Storage::disk('public')->delete($shot->path);
        }

        $movie->genres()->detach();
        $movie->countries()->detach();
        $movie->delete();

        return back()->with('success', 'Контент удалён');
    }

    /* ================= HELPERS ================= */

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;

        while (
            Movie::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * 🔥 КЛЮЧЕВОЙ ФИКС (БЕЗ mkdir)
     * storage/app/private/uploads/movies/{id}/source.mp4
     */
    private function handleVideoUpload(Request $request, Movie $movie): void
    {
        $file = $request->file('source_video');

        if (!$file) {
            abort(422, 'Файл source_video не получен');
        }

        if (!str_starts_with($file->getMimeType(), 'video/')) {
            abort(422, 'Файл не является видео');
        }

        // ✅ корректное создание директории через Laravel
        $absoluteDir = storage_path("app/private/uploads/movies/{$movie->id}");
        File::ensureDirectoryExists($absoluteDir, 0755);

        // ✅ жёстко сохраняем как source.mp4
        $file->move($absoluteDir, 'source.mp4');

        // 🔄 сброс состояния
        $movie->update([
            'status'   => 'processing',
            'hls_path' => null,
        ]);

        // 🚀 dispatch единственного job
        ConvertMovieToHls::dispatch($movie->id);
    }

    private function validated(Request $request, ?int $movieId = null): array
    {
        return $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'original_title' => ['nullable', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],

            'year'           => ['nullable', 'integer', 'between:1900,' . now()->year],
            'duration'       => ['nullable', 'integer', 'min:1'],
            'rating'         => ['nullable', 'numeric', 'between:0,10'],

            'is_series'      => ['boolean'],
            'status'         => ['required', Rule::in(['draft','processing','ready','blocked'])],

            'poster'         => ['nullable', 'image', 'max:4096'],
            'screenshots.*'  => ['nullable', 'image', 'max:4096'],
            'source_video'   => ['nullable', 'file', 'mimetypes:video/*'],

            'genres'         => ['array'],
            'genres.*'       => ['integer', 'exists:genres,id'],

            'countries'      => ['array'],
            'countries.*'    => ['integer', 'exists:countries,id'],
        ]);
    }
}
