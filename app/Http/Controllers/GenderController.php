<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Gender::query();
        if ($request->filled('search')) {

            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status);
        }

        $genders = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Gender/index', [
            'genders' => $genders,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Gender/create');
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
        Gender::create($validated);

        return redirect()->route('genders.index')->with('success', 's');
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
        $gender = Gender::findOrFail($id);

        return Inertia::render('Gender/edit', compact('gender'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $gender = Gender::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
        ]);
        $gender->update($validated);

        return redirect()->route('genders.index')->with('success', 's');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gender = Gender::findOrFail($id);

        $gender->status = 0;
        $gender->save();

        return redirect()->route('genders.index')->with('success', 'Eliminando');
    }
}
