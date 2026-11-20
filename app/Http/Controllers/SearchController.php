<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index(Request $request)
    {
        return Inertia::render('Search', $this->catalog->search(
            $request->string('q')->toString(),
            $request->user()
        ));
    }
}
