<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Song::query()->with('gender', 'artist');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('thumbnail', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('select_genders')) {
            $query->where('select_genders', $request->select_genders);
        }

        $songs = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Song/index', [
            'songs' => $songs,
            'filters' => $request->only(['search','select_genders'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genders = Gender::all();
        $artists = User::all();

        // Pasamos el ID del usuario autenticado para que el formulario lo use por defecto
        $authId = Auth::id();
        return Inertia::render('Song/create', compact('genders', 'artists', 'authId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'artist_id' => 'nullable|numeric|exists:users,id',
            'gender_id' => 'required|numeric|exists:genders,id',
            'name' => 'required|string',
            'thumbnail' => 'nullable|image',
            'lyrics' => 'required|string',
            'audio' => 'required|string',
        ]);

        //Si artist_id está vacío, asignamos el Auth::id()
        $validated['artist_id'] = $validated['artist_id'] ?? Auth::id();

        //Asignamos quién creó el registro (user_id)
        $validated['user_id'] = Auth::id();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails','public');
        }

        Song::create($validated);

        return redirect()->route('songs.index')->with('success', 'Song creating correctly');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
