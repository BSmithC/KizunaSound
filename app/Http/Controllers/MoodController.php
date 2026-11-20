<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;
use Inertia\Inertia;

class MoodController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index()
    {
        return Inertia::render('Mood', [
            'moods' => $this->catalog->moods(),
        ]);
    }
}
