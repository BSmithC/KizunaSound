<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PlaylistController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Playlist::class);

        $query = Playlist::query()
            ->with('owner')
            ->withCount('songs')
            ->where(function ($q) {
                $q->where('user_id', Auth::id())
                    ->orWhere('is_public', true);
            });

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->exists('is_public') && $request->input('is_public') !== '') {
            $query->where('is_public', filter_var($request->input('is_public'), FILTER_VALIDATE_BOOLEAN));
        }

        $playlists = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Playlist/index', [
            'playlists' => $playlists,
            'filters' => $request->only(['search', 'is_public']),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Playlist::class);

        return Inertia::render('Playlist/create', [
            'songs' => $this->availableSongs(),
            'playlist' => null,
            'selectedSongs' => [],
            'canManage' => true,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Playlist::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'img' => 'nullable|image|max:2048',
            'is_public' => 'sometimes|boolean',
            'is_collaborative' => 'sometimes|boolean',
            'song_id' => 'nullable|integer|exists:songs,id',
            'songs' => 'nullable|array',
            'songs.*' => 'integer|exists:songs,id',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['description'] = $validated['description'] ?? '';
        $validated['is_public'] = $request->boolean('is_public');
        $validated['is_collaborative'] = $request->boolean('is_collaborative');
        $validated['img'] = '';

        if ($request->hasFile('img')) {
            $validated['img'] = $request->file('img')->store('covers', 'public');
        }

        $playlist = Playlist::create($validated);

        $this->syncSongs($playlist, $request);

        return redirect()->route('playlists.show', $playlist)->with('toast', 'Playlist creada correctamente.');
    }

    public function show(Playlist $playlist)
    {
        $this->authorize('view', $playlist);

        $playlist->load([
            'owner',
            'songs' => fn ($query) => $query->with(['artist', 'genre']),
        ])->loadCount('songs');

        return Inertia::render('Playlist/show', [
            'playlist' => $playlist,
            'canManage' => Auth::user()->can('update', $playlist),
        ]);
    }

    public function edit(Playlist $playlist)
    {
        $this->authorize('update', $playlist);

        return Inertia::render('Playlist/edit', [
            'playlist' => $playlist->load(['songs']),
            'songs' => $this->availableSongs(),
            'selectedSongs' => $playlist->songs->pluck('id')->all(),
            'canManage' => true,
        ]);
    }

    public function update(Request $request, Playlist $playlist)
    {
        $this->authorize('update', $playlist);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'img' => 'nullable|image|max:2048',
            'is_public' => 'sometimes|boolean',
            'is_collaborative' => 'sometimes|boolean',
            'song_id' => 'nullable|integer|exists:songs,id',
            'songs' => 'nullable|array',
            'songs.*' => 'integer|exists:songs,id',
        ]);

        $validated['description'] = $validated['description'] ?? '';
        $validated['is_public'] = $request->boolean('is_public', $playlist->is_public);
        $validated['is_collaborative'] = $request->boolean('is_collaborative', $playlist->is_collaborative);

        if (! $playlist->user_id) {
            $validated['user_id'] = Auth::id();
        }

        if ($request->hasFile('img')) {
            $oldImage = $playlist->img;
            $validated['img'] = $request->file('img')->store('covers', 'public');
        } else {
            unset($validated['img']);
        }

        $playlist->update($validated);

        if (isset($validated['img']) && $validated['img'] !== $oldImage) {
            $this->deleteFile($oldImage);
        }

        $this->syncSongs($playlist, $request);

        return redirect()->route('playlists.show', $playlist)->with('toast', 'Playlist actualizada correctamente.');
    }

    public function destroy(Playlist $playlist)
    {
        $this->authorize('delete', $playlist);

        $playlist->delete();

        return redirect()->route('playlists.index')->with('toast', 'Playlist eliminada correctamente.');
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

    private function syncSongs(Playlist $playlist, Request $request): void
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
            $songId => [
                'position' => $index + 1,
                'added_by' => Auth::id(),
            ],
        ])->all();

        $playlist->songs()->sync($pivot);
    }

    private function deleteFile(?string $path): void
    {
        if (! empty($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
