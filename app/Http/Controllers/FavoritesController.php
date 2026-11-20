<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;
use Inertia\Inertia;

class FavoritesController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index()
    {
        return Inertia::render('Favorites', [
            'songs' => $this->catalog->library(request()->user())['favorites'],
        ]);
    }
}
