<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index()
    {
        return Inertia::render('Home', $this->catalog->homePayload(request()->user()));
    }
}
