<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;
use Inertia\Inertia;

class LibraryController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index()
    {
        return Inertia::render('Library', $this->catalog->library(request()->user()));
    }
}
