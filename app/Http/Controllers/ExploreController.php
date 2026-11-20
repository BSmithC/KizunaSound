<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExploreController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index(Request $request)
    {
        return Inertia::render('Explore', $this->catalog->explore($request->string('genre')->toString() ?: null));
    }
}
