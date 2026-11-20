<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\ListeningHistory;
use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CatalogService
{
    public function homePayload($user): array
    {
        return [
            'recommendedSongs' => $this->recommendedSongs($user),
            'recentSongs' => $this->recentSongs($user),
            'popularAlbums' => $this->popularAlbums(),
            'featuredArtists' => $this->featuredArtists(),
            'recommendedPlaylists' => $this->recommendedPlaylists(),
            'newReleases' => $this->newReleases(),
            'genres' => $this->genres(),
            'moods' => $this->moods(),
        ];
    }

    public function recommendedSongs($user): Collection
    {
        $genreIds = DB::table('favorites')
            ->join('songs', 'songs.id', '=', 'favorites.song_id')
            ->where('favorites.user_id', $user->id)
            ->pluck('songs.gender_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $artistIds = DB::table('followed_artists')
            ->where('user_id', $user->id)
            ->pluck('artist_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $listenedIds = DB::table('listening_history')
            ->where('user_id', $user->id)
            ->pluck('song_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        return Song::query()
            ->with(['artist', 'genre'])
            ->where('is_public', true)
            ->when($genreIds || $artistIds, function ($q) use ($genreIds, $artistIds) {
                $q->where(function ($sub) use ($genreIds, $artistIds) {
                    $sub->when($genreIds, fn ($inner) => $inner->whereIn('gender_id', $genreIds));

                    if ($artistIds) {
                        $genreIds
                            ? $sub->orWhereIn('artist_ref_id', $artistIds)
                            : $sub->whereIn('artist_ref_id', $artistIds);
                    }
                });
            })
            ->when($listenedIds, fn ($q) => $q->whereNotIn('id', $listenedIds))
            ->orderByDesc('play_count')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();
    }

    public function recentSongs($user): Collection
    {
        return $this->songsFromRecentHistory($user, 8);
    }

    /**
     * Devuelve los ids de canción escuchados más recientemente, sin repetirlos.
     */
    private function recentSongIds($user, int $limit): Collection
    {
        return ListeningHistory::query()
            ->where('user_id', $user->id)
            ->select('song_id', DB::raw('MAX(listened_at) as last_listened_at'))
            ->groupBy('song_id')
            ->orderByDesc('last_listened_at')
            ->orderByDesc('song_id')
            ->limit($limit)
            ->pluck('song_id');
    }

    /**
     * Hidrata las canciones conservando el orden de escucha más reciente.
     */
    private function songsFromRecentHistory($user, int $limit): Collection
    {
        $ids = $this->recentSongIds($user, $limit);

        if ($ids->isEmpty()) {
            return new Collection;
        }

        $songs = Song::query()
            ->with('artist', 'genre')
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        return $ids
            ->map(fn ($id) => $songs->get($id))
            ->filter()
            ->values();
    }

    public function popularAlbums(): Collection
    {
        return Album::query()
            ->with('artist')
            ->withCount('savedBy')
            ->where('is_public', true)
            ->orderByDesc('saved_by_count')
            ->latest()
            ->limit(8)
            ->get();
    }

    public function featuredArtists(): Collection
    {
        return Artist::query()
            ->where('is_active', true)
            ->withCount('songs')
            ->orderByDesc('songs_count')
            ->latest()
            ->limit(8)
            ->get();
    }

    public function recommendedPlaylists(): Collection
    {
        return Playlist::query()
            ->with('owner')
            ->withCount('songs')
            ->where('is_public', true)
            ->orderByDesc('songs_count')
            ->latest()
            ->limit(8)
            ->get();
    }

    public function newReleases(): Collection
    {
        return Song::query()
            ->with(['artist', 'genre'])
            ->where('is_public', true)
            ->orderByDesc('release_date')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();
    }

    public function genres(): Collection
    {
        return Genre::query()
            ->where('active', true)
            ->withCount('songs')
            ->orderByDesc('songs_count')
            ->get();
    }

    /**
     * Agrupa canciones por el estado de ánimo real asignado en el catálogo.
     * No se inventan Estados ni se asocian canciones ficticias.
     */
    public function moods(): Collection
    {
        return Song::query()
            ->with(['artist', 'genre'])
            ->where('is_public', true)
            ->whereNotNull('mood')
            ->where('mood', '!=', '')
            ->orderBy('mood')
            ->orderByDesc('play_count')
            ->get()
            ->groupBy('mood')
            ->map(fn ($songs, $mood) => [
                'mood' => $mood,
                'songs' => $songs->values(),
            ])
            ->values();
    }

    public function explore(?string $genreSlug): array
    {
        $genre = $genreSlug ? Genre::where('slug', $genreSlug)->first() : null;

        $songs = Song::query()
            ->with(['artist', 'genre'])
            ->where('is_public', true)
            ->when($genre, fn ($q) => $q->where('gender_id', $genre->id))
            ->orderByDesc('play_count')
            ->limit(24)
            ->get();

        return [
            'songs' => $songs,
            'genres' => $this->genres(),
            'artists' => $this->featuredArtists(),
            'albums' => $this->popularAlbums(),
            'activeGenre' => $genre,
        ];
    }

    public function search(string $query, $user): array
    {
        $term = trim($query);

        if (mb_strlen($term) < 2) {
            return [
                'query' => $term,
                'songs' => collect(),
                'artists' => collect(),
                'albums' => collect(),
                'playlists' => collect(),
                'genres' => collect(),
            ];
        }

        $like = '%'.$term.'%';

        return [
            'query' => $term,
            'songs' => Song::query()
                ->with(['artist', 'genre'])
                ->where('is_public', true)
                ->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('lyrics', 'like', $like))
                ->limit(10)
                ->get(),
            'artists' => Artist::query()
                ->where('is_active', true)
                ->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('bio', 'like', $like))
                ->limit(10)
                ->get(),
            'albums' => Album::query()
                ->with('artist')
                ->where('is_public', true)
                ->where('name', 'like', $like)
                ->limit(10)
                ->get(),
            'playlists' => Playlist::query()
                ->with('owner')
                ->where('name', 'like', $like)
                ->where(fn ($q) => $q->where('is_public', true)->orWhere('user_id', $user->id))
                ->limit(10)
                ->get(),
            'genres' => Genre::query()
                ->where('active', true)
                ->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('description', 'like', $like))
                ->limit(10)
                ->get(),
        ];
    }

    public function library($user): array
    {
        return [
            'favorites' => Song::query()
                ->with(['artist', 'genre'])
                ->whereHas('favorites', fn ($q) => $q->where('user_id', $user->id))
                ->orderByDesc('created_at')
                ->get(),
            'albums' => Album::query()
                ->with('artist')
                ->whereHas('savedBy', fn ($q) => $q->where('user_id', $user->id))
                ->get(),
            'artists' => Artist::query()
                ->whereHas('followers', fn ($q) => $q->where('user_id', $user->id))
                ->get(),
            'playlists' => Playlist::query()
                ->withCount('songs')
                ->where('user_id', $user->id)
                ->latest()
                ->get(),
            'history' => $this->songsFromRecentHistory($user, 30),
        ];
    }
}
