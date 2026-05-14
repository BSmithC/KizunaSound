<?php

namespace App\Http\Controllers;

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
    /**
     * Display a listing of the resource.
     */
    public function stream(Song $song): StreamedResponse
    {
        // Cargar la canción desde la base de datos usando el ID recibido
        // Laravel ya lo hace automáticamente con Route Model Binding.

        // Verificar que el archivo exista
        if (! Storage::disk('public')->exists($song->audio)) {
            abort(404, 'Archivo de audio no encontrado.');
        }

        $path = Storage::disk('public')->path($song->audio);
        $mime = mime_content_type($path);
        $size = filesize($path);

        // Soporte para reproducción por rangos (necesario para el <audio>)
        $start = 0;
        $end = $size - 1;

        if (request()->hasHeader('Range')) {
            preg_match('/bytes=(\d+)-(\d*)/', request()->header('Range'), $matches);

            $start = (int) $matches[1];

            if (! empty($matches[2])) {
                $end = (int) $matches[2];
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

        // Respuesta completa
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
            'filters' => $request->only(['search', 'select_genders']),
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
            'artist_id' => 'required|numeric|exists:users,id',
            'gender_id' => 'required|numeric|exists:genders,id',
            'name' => 'required|string',
            'thumbnail' => 'nullable|image',
            'lyrics' => 'required|string',
            'audio' => 'required|mimes:mp3,wav,ogg|max:10240', // máximo 10MB
        ]);

        // Si artist_id está vacío, asignamos el Auth::id()
        $validated['artist_id'] = $validated['artist_id'] ?? Auth::id();

        // Asignamos quién creó el registro (user_id)
        $validated['user_id'] = Auth::id();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }
        if ($request->hasFile('audio')) {
            $validated['audio'] = $request->file('audio')->store('songs', 'public');
        }

        Song::create($validated);

        return redirect()->route('songs.index')->with('toast', 'Song creating correctly');
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
        $genders = Gender::all();
        $artists = User::all();
        $song = Song::findOrFail($id);
        $song->load('artist', 'gender');

        return Inertia::render('Song/edit', compact('song', 'genders', 'artists'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $song = Song::findOrFail($id);

        $validated = $request->validate([
            'artist_id' => 'required|numeric|exists:users,id',
            'gender_id' => 'required|numeric|exists:genders,id',
            'name' => 'required|string',
            'thumbnail' => 'nullable|image',
            'lyrics' => 'required|string',
            'audio' => 'required|string',
        ]);

        // Si artist_id está vacío, asignamos el Auth::id()
        $validated['artist_id'] = $validated['artist_id'] ?? Auth::id();

        // Asignamos quién creó el registro (user_id)
        $validated['user_id'] = Auth::id();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $song->update($validated);

        return redirect()->back()->route('songs.index')->with('toast', 'Song  correctly');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
