<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Playlist::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "{$request->search}")
                    ->orWhere('description', 'like', "{$request->search}");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $playlists = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Playlist/index', [
            'playlists' => $playlists,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Playlist/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
        ]);
        Playlist::create($validated);

        return redirect()->route('playlists.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $playlist = Playlist::findOrFail($id);

        return Inertia::render('Playlist/edit', compact('playlist'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $playlist = Playlist::findOrFail($id);

        $validated = $request->validate([
            'name' => 'requierd|string',
            'description' => 'requierd|string',
        ]);

        $playlist->update($validated);

        return redirect()->route('playlists.index')->with('sucess', 'a');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $playlist = Playlist::findOrFail($id);

        $playlist->status = 0;
        $playlist->save();

        return redirect()->route('playlists.index')->with('sucess', 'a');
    }
}
