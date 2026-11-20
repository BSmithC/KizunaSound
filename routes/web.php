<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\GenderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryActionController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\MoodController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SongController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check() && auth()->user()->hasVerifiedEmail()) {
        return redirect()->route('home');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/library', [LibraryController::class, 'index'])->name('library');
    Route::get('/favorites', [FavoritesController::class, 'index'])->name('favorites');
    Route::get('/mood', [MoodController::class, 'index'])->name('mood.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('/songs', SongController::class);
    Route::resource('/albums', AlbumController::class);
    Route::resource('/playlists', PlaylistController::class);
    Route::resource('/genders', GenderController::class);
    Route::resource('/artists', ArtistController::class);
    Route::get('/songs/{song}/stream', [SongController::class, 'stream'])->name('songs.stream');

    Route::post('/library/favorites/{song}', [LibraryActionController::class, 'toggleFavorite'])->name('library.favorites.toggle');
    Route::post('/library/history/{song}', [LibraryActionController::class, 'recordHistory'])->name('library.history.record');
    Route::post('/library/albums/{album}', [LibraryActionController::class, 'toggleAlbum'])->name('library.albums.toggle');
    Route::post('/library/artists/{artist}', [LibraryActionController::class, 'toggleArtist'])->name('library.artists.toggle');
    Route::post('/playlists/{playlist}/songs/{song}', [LibraryActionController::class, 'togglePlaylistSong'])->name('playlists.songs.toggle');
});

require __DIR__.'/auth.php';
