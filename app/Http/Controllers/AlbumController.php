<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\SavedAlbum;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AlbumController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Album::class);

        $query = Album::query()->with(['artist', 'songs'])->withCount('songs', 'savedBy');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('license', 'like', "%{$search}%")
                    ->orWhereHas('artist', fn ($artist) => $artist->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->exists('is_public') && $request->input('is_public') !== '') {
            $query->where('is_public', filter_var($request->input('is_public'), FILTER_VALIDATE_BOOLEAN));
        }

        $albums = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Album/index', [
            'albums' => $albums,
            'filters' => $request->only(['search', 'is_public']),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Album::class);

        return Inertia::render('Album/create', [
            'songs' => $this->availableSongs(),
            'artists' => Artist::query()->orderBy('name')->get(['id', 'name']),
            'album' => null,
            'selectedSongs' => [],
            'canManage' => true,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Album::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license' => 'nullable|string|max:255',
            'img' => 'nullable|image|max:2048',
            'artist_id' => 'nullable|integer|exists:artists,id',
            'release_date' => 'nullable|date',
            'is_public' => 'sometimes|boolean',
            'artists' => 'nullable|array',
            'artists.*' => 'string|max:255',
            'song_id' => 'nullable|integer|exists:songs,id',
            'songs' => 'nullable|array',
            'songs.*' => 'integer|exists:songs,id',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_public'] = $request->boolean('is_public', true);
        $validated['artists'] = $validated['artists'] ?? [];
        $validated['img'] = '';

        if ($request->hasFile('img')) {
            $validated['img'] = $request->file('img')->store('covers', 'public');
        }

        $album = Album::create($validated);

        $this->syncSongs($album, $request);

        return redirect()->route('albums.show', $album)->with('toast', 'Álbum creado correctamente.');
    }

    public function show(Album $album)
    {
        $this->authorize('view', $album);

        $album->load([
            'artist',
            'owner',
            'songs' => fn ($query) => $query->with(['artist', 'genre']),
        ])->loadCount('songs', 'savedBy');

        return Inertia::render('Album/show', [
            'album' => $album,
            'canManage' => Auth::user()->can('update', $album),
            'isSaved' => SavedAlbum::where('user_id', Auth::id())
                ->where('album_id', $album->id)
                ->exists(),
        ]);
    }

    public function edit(Album $album)
    {
        $this->authorize('update', $album);

        // No hay vista Album/edit.vue en el contrato: se reutiliza create.vue en modo edición.
        return Inertia::render('Album/create', [
            'album' => $album->load(['artist', 'songs']),
            'songs' => $this->availableSongs(),
            'artists' => Artist::query()->orderBy('name')->get(['id', 'name']),
            'selectedSongs' => $album->songs->pluck('id')->all(),
            'canManage' => true,
        ]);
    }

    public function update(Request $request, Album $album)
    {
        $this->authorize('update', $album);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license' => 'nullable|string|max:255',
            'img' => 'nullable|image|max:2048',
            'artist_id' => 'nullable|integer|exists:artists,id',
            'release_date' => 'nullable|date',
            'is_public' => 'sometimes|boolean',
            'artists' => 'nullable|array',
            'artists.*' => 'string|max:255',
            'song_id' => 'nullable|integer|exists:songs,id',
            'songs' => 'nullable|array',
            'songs.*' => 'integer|exists:songs,id',
        ]);

        $validated['is_public'] = $request->boolean('is_public', $album->is_public);
        $validated['artists'] = $validated['artists'] ?? [];

        if (! $album->user_id) {
            $validated['user_id'] = Auth::id();
        }

        if ($request->hasFile('img')) {
            $oldImage = $album->img;
            $validated['img'] = $request->file('img')->store('covers', 'public');
        } else {
            unset($validated['img']);
        }

        $album->update($validated);

        if (isset($validated['img']) && $validated['img'] !== $oldImage) {
            $this->deleteFile($oldImage);
        }

        $this->syncSongs($album, $request);

        return redirect()->route('albums.show', $album)->with('toast', 'Álbum actualizado correctamente.');
    }

    public function destroy(Album $album)
    {
        $this->authorize('delete', $album);

        $album->delete();

        return redirect()->route('albums.index')->with('toast', 'Álbum eliminado correctamente.');
    }

    private function availableSongs()
    {
        return Song::query()
            ->with(['artist', 'genre'])
            ->where(function ($query) {
                $query->where('is_public', true)
                    ->orWhere('artist_id', Auth::id());
            })
            ->orderBy('name')
            ->get(['id', 'name', 'thumbnail', 'artist_ref_id', 'gender_id', 'duration_seconds']);
    }

    private function syncSongs(Album $album, Request $request): void
    {
        $ids = collect($request->input('songs', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty() && $request->filled('song_id')) {
            $ids->push((int) $request->input('song_id'));
        }

        $pivot = $ids->mapWithKeys(fn (int $songId, int $index) => [
            $songId => ['position' => $index + 1],
        ])->all();

        $album->songs()->sync($pivot);
    }

    private function deleteFile(?string $path): void
    {
        if (! empty($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
