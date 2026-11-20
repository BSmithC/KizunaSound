<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Favorite;
use App\Models\Gender;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SongController extends Controller
{
    private const AUDIO_RULES = 'nullable|file|mimes:mp3,wav,ogg,m4a|max:10240';

    public function index(Request $request)
    {
        $this->authorize('viewAny', Song::class);

        $query = Song::query()->with(['artist', 'genre']);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('lyrics', 'like', "%{$search}%")
                    ->orWhere('mood', 'like', "%{$search}%")
                    ->orWhereHas('genre', fn ($genre) => $genre->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('artist', fn ($artist) => $artist->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('gender_id')) {
            $query->where('gender_id', (int) $request->input('gender_id'));
        }

        if ($request->filled('mood')) {
            $query->where('mood', $request->string('mood')->toString());
        }

        $songs = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Song/index', [
            'songs' => $songs,
            'filters' => $request->only(['search', 'gender_id', 'mood']),
            'genders' => Gender::query()->where('active', true)->orderBy('name')->get(['id', 'name']),
            'moodOptions' => $this->moodOptions(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Song::class);

        return Inertia::render('Song/create', [
            'genders' => Gender::query()->where('active', true)->orderBy('name')->get(['id', 'name']),
            'artists' => Artist::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'uploaders' => User::query()->orderBy('name')->get(['id', 'name']),
            'moodOptions' => $this->moodOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Song::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender_id' => 'required|integer|exists:genders,id',
            'artist_ref_id' => 'nullable|integer|exists:artists,id',
            'lyrics' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'audio' => 'required|file|mimes:mp3,wav,ogg,m4a|max:10240',
            'mood' => 'nullable|string|max:50',
            'license' => 'nullable|string|max:255',
            'release_date' => 'nullable|date',
            'duration_seconds' => 'nullable|integer|min:0',
            'is_public' => 'sometimes|boolean',
        ]);

        // El usuario autenticado es siempre el uploader (artist_id).
        $validated['artist_id'] = Auth::id();
        $validated['artist_ref_id'] = $validated['artist_ref_id'] ?? null;
        $validated['is_public'] = $request->boolean('is_public', true);

        // songs.thumbnail no admite NULL en la BD; una cadena vacía activa el fallback visual.
        $validated['thumbnail'] = '';

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $validated['audio'] = $request->file('audio')->store('songs', 'public');

        Song::create($validated);

        return redirect()->route('songs.index')->with('toast', 'Canción creada correctamente.');
    }

    public function show(Song $song)
    {
        $this->authorize('view', $song);

        $song->load([
            'artist',
            'genre',
            'uploader',
            'albums' => fn ($query) => $query->select('albums.id', 'albums.name', 'albums.img')->limit(8),
        ]);

        return Inertia::render('Song/show', [
            'song' => $song,
            'canManage' => Auth::user()->can('update', $song),
            'isFavorite' => Favorite::where('user_id', Auth::id())
                ->where('song_id', $song->id)
                ->exists(),
        ]);
    }

    public function edit(Song $song)
    {
        $this->authorize('update', $song);

        return Inertia::render('Song/edit', [
            'song' => $song->load(['artist', 'genre']),
            'genders' => Gender::query()->where('active', true)->orderBy('name')->get(['id', 'name']),
            'artists' => Artist::query()->orderBy('name')->get(['id', 'name']),
            'uploaders' => User::query()->orderBy('name')->get(['id', 'name']),
            'moodOptions' => $this->moodOptions(),
        ]);
    }

    public function update(Request $request, Song $song)
    {
        $this->authorize('update', $song);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender_id' => 'required|integer|exists:genders,id',
            'artist_ref_id' => 'nullable|integer|exists:artists,id',
            'lyrics' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'audio' => self::AUDIO_RULES,
            'mood' => 'nullable|string|max:50',
            'license' => 'nullable|string|max:255',
            'release_date' => 'nullable|date',
            'duration_seconds' => 'nullable|integer|min:0',
            'is_public' => 'sometimes|boolean',
        ]);

        $validated['artist_ref_id'] = $validated['artist_ref_id'] ?? null;
        $validated['is_public'] = $request->boolean('is_public', $song->is_public);

        $oldThumbnail = $song->thumbnail;
        $oldAudio = $song->audio;

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        } else {
            unset($validated['thumbnail']);
        }

        if ($request->hasFile('audio')) {
            $validated['audio'] = $request->file('audio')->store('songs', 'public');
        } else {
            unset($validated['audio']);
        }

        $song->update($validated);

        if (! empty($validated['thumbnail']) && $validated['thumbnail'] !== $oldThumbnail) {
            $this->deleteFile($oldThumbnail);
        }

        if (! empty($validated['audio']) && $validated['audio'] !== $oldAudio) {
            $this->deleteFile($oldAudio);
        }

        return redirect()->route('songs.show', $song)->with('toast', 'Canción actualizada correctamente.');
    }

    public function destroy(Song $song)
    {
        $this->authorize('delete', $song);

        $this->deleteFile($song->audio);
        $this->deleteFile($song->thumbnail);

        $song->delete();

        return redirect()->route('songs.index')->with('toast', 'Canción eliminada correctamente.');
    }

    public function stream(Song $song): StreamedResponse
    {
        if (! Storage::disk('public')->exists($song->audio)) {
            abort(404, 'Archivo de audio no encontrado.');
        }

        $path = Storage::disk('public')->path($song->audio);
        $mime = mime_content_type($path) ?: 'application/octet-stream';
        $size = filesize($path);

        $start = 0;
        $end = $size - 1;

        if (request()->hasHeader('Range')
            && preg_match('/bytes=(\d+)-(\d*)/', (string) request()->header('Range'), $matches)) {
            $start = min((int) $matches[1], $size - 1);
            $end = ! empty($matches[2]) ? min((int) $matches[2], $size - 1) : $size - 1;

            if ($end < $start) {
                $end = $start;
            }

            $length = $end - $start + 1;

            return response()->stream(function () use ($path, $start, $length) {
                $handle = fopen($path, 'rb');
                fseek($handle, $start);

                $remaining = $length;
                $chunkSize = 8192;

                while (! feof($handle) && $remaining > 0) {
                    $read = min($chunkSize, $remaining);
                    echo fread($handle, $read);
                    flush();
                    $remaining -= $read;
                }

                fclose($handle);
            }, 206, [
                'Content-Type' => $mime,
                'Content-Length' => $length,
                'Content-Range' => "bytes {$start}-{$end}/{$size}",
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ]);
        }

        return response()->stream(function () use ($path) {
            $handle = fopen($path, 'rb');

            while (! feof($handle)) {
                echo fread($handle, 8192);
                flush();
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => $mime,
            'Content-Length' => $size,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    private function moodOptions(): array
    {
        return Song::query()
            ->whereNotNull('mood')
            ->where('mood', '!=', '')
            ->distinct()
            ->orderBy('mood')
            ->pluck('mood')
            ->values()
            ->all();
    }

    private function deleteFile(?string $path): void
    {
        if (! empty($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
