<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class GenderController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Gender::class);

        $query = Gender::query()->withCount('songs');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->exists('active') && $request->input('active') !== '') {
            $query->where('active', filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN));
        }

        $genders = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Gender/index', [
            'genders' => $genders,
            'filters' => $request->only(['search', 'active']),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Gender::class);

        return Inertia::render('Gender/create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Gender::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:genders,name',
            'description' => 'nullable|string',
            'active' => 'sometimes|boolean',
        ]);

        $validated['slug'] = $this->uniqueSlug($validated['name']);
        $validated['active'] = $request->boolean('active', true);

        Gender::create($validated);

        return redirect()->route('genders.index')->with('toast', 'Género creado correctamente.');
    }

    public function show(Gender $gender)
    {
        $gender->loadCount('songs')->load([
            'songs' => fn ($query) => $query->with(['artist', 'genre'])
                ->where('is_public', true)
                ->latest()
                ->limit(8),
        ]);

        return Inertia::render('Gender/show', [
            'gender' => $gender,
            'canManage' => true,
        ]);
    }

    public function edit(Gender $gender)
    {
        $this->authorize('update', Gender::class);

        return Inertia::render('Gender/edit', compact('gender'));
    }

    public function update(Request $request, Gender $gender)
    {
        $this->authorize('update', Gender::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:genders,name,'.$gender->id,
            'description' => 'nullable|string',
            'active' => 'sometimes|boolean',
        ]);

        if (Str::slug($validated['name']) !== $gender->slug) {
            $validated['slug'] = $this->uniqueSlug($validated['name'], $gender->id);
        }

        $validated['active'] = $request->boolean('active', $gender->active);

        $gender->update($validated);

        return redirect()->route('genders.index')->with('toast', 'Género actualizado correctamente.');
    }

    public function destroy(Gender $gender)
    {
        $this->authorize('delete', Gender::class);

        // No existe columna "status": el borrado lógico se gestiona alternando "active".
        $gender->active = ! $gender->active;
        $gender->save();

        return redirect()->route('genders.index')
            ->with('toast', $gender->active ? 'Género activado correctamente.' : 'Género desactivado correctamente.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'genero';
        $slug = $base;
        $counter = 2;

        while (Gender::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
