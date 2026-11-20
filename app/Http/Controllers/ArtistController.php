<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\FollowedArtist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ArtistController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Artist::class);

        $query = Artist::query()->withCount('songs');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->exists('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $artists = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Artists/index', [
            'artists' => $artists,
            'filters' => $request->only(['search', 'is_active']),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Artist::class);

        return Inertia::render('Artists/create', [
            'artist' => null,
            'canManage' => true,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Artist::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:artists,name',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'cover' => 'nullable|image|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['slug'] = $this->uniqueSlug($validated['name']);
        $validated['bio'] = $validated['bio'] ?? '';
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['user_id'] = $this->ownerForNewArtist();

        $validated['image'] = '';
        $validated['cover'] = '';

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('artists', 'public');
        }

        if ($request->hasFile('cover')) {
            $validated['cover'] = $request->file('cover')->store('artists/covers', 'public');
        }

        $artist = Artist::create($validated);

        return redirect()->route('artists.show', $artist)->with('toast', 'Artista creado correctamente.');
    }

    public function show(Artist $artist)
    {
        $this->authorize('view', $artist);

        $artist->load([
            'albums' => fn ($query) => $query->select('albums.id', 'albums.name', 'albums.img', 'albums.artist_id')
                ->latest()
                ->limit(8),
            'songs' => fn ($query) => $query->with(['genre', 'uploader'])
                ->where('is_public', true)
                ->latest()
                ->limit(20),
        ])->loadCount('songs', 'followers');

        return Inertia::render('Artists/show', [
            'artist' => $artist,
            'canManage' => Auth::user()->can('update', $artist),
            'isFollowing' => FollowedArtist::where('user_id', Auth::id())
                ->where('artist_id', $artist->id)
                ->exists(),
        ]);
    }

    public function edit(Artist $artist)
    {
        $this->authorize('update', $artist);

        // No hay vista Artists/edit.vue en el contrato: se reutiliza create.vue en modo edición.
        return Inertia::render('Artists/create', [
            'artist' => $artist,
            'canManage' => true,
        ]);
    }

    public function update(Request $request, Artist $artist)
    {
        $this->authorize('update', $artist);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:artists,name,'.$artist->id,
            'bio' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'cover' => 'nullable|image|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        if (Str::slug($validated['name']) !== $artist->slug) {
            $validated['slug'] = $this->uniqueSlug($validated['name'], $artist->id);
        }

        $validated['bio'] = $validated['bio'] ?? '';
        $validated['is_active'] = $request->boolean('is_active', $artist->is_active);

        $oldImage = $artist->image;
        $oldCover = $artist->cover;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('artists', 'public');
        } else {
            unset($validated['image']);
        }

        if ($request->hasFile('cover')) {
            $validated['cover'] = $request->file('cover')->store('artists/covers', 'public');
        } else {
            unset($validated['cover']);
        }

        $artist->update($validated);

        if (isset($validated['image']) && $validated['image'] !== $oldImage) {
            $this->deleteFile($oldImage);
        }

        if (isset($validated['cover']) && $validated['cover'] !== $oldCover) {
            $this->deleteFile($oldCover);
        }

        return redirect()->route('artists.show', $artist)->with('toast', 'Artista actualizado correctamente.');
    }

    public function destroy(Artist $artist)
    {
        $this->authorize('delete', $artist);

        $artist->delete();

        return redirect()->route('artists.index')->with('toast', 'Artista eliminado correctamente.');
    }

    private function ownerForNewArtist(): ?int
    {
        // artists.user_id es único: sólo se vincula automáticamente el primer artista del usuario.
        if (Artist::withTrashed()->where('user_id', Auth::id())->exists()) {
            return null;
        }

        return Auth::id();
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'artista';
        $slug = $base;
        $counter = 2;

        while (Artist::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }

    private function deleteFile(?string $path): void
    {
        if (! empty($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
