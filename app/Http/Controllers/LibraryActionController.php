<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Favorite;
use App\Models\FollowedArtist;
use App\Models\ListeningHistory;
use App\Models\Playlist;
use App\Models\SavedAlbum;
use App\Models\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LibraryActionController extends Controller
{
    public function toggleFavorite(Song $song): JsonResponse
    {
        $favorite = Favorite::where('user_id', auth()->id())
            ->where('song_id', $song->id)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return response()->json(['favorite' => false]);
        }

        Favorite::create([
            'user_id' => auth()->id(),
            'song_id' => $song->id,
        ]);

        return response()->json(['favorite' => true]);
    }

    public function recordHistory(Request $request, Song $song): JsonResponse
    {
        $recent = ListeningHistory::where('user_id', auth()->id())
            ->where('song_id', $song->id)
            ->where('listened_at', '>=', now()->subSeconds(30))
            ->exists();

        if (! $recent) {
            ListeningHistory::create([
                'user_id' => auth()->id(),
                'song_id' => $song->id,
                'listened_at' => now(),
                'source' => $request->string('source')->toString() ?: 'player',
            ]);

            $song->increment('play_count');
        }

        return response()->json(['ok' => true, 'play_count' => $song->play_count]);
    }

    public function toggleAlbum(Album $album): JsonResponse
    {
        $saved = SavedAlbum::where('user_id', auth()->id())
            ->where('album_id', $album->id)
            ->first();

        if ($saved) {
            $saved->delete();

            return response()->json(['saved' => false]);
        }

        SavedAlbum::create([
            'user_id' => auth()->id(),
            'album_id' => $album->id,
        ]);

        return response()->json(['saved' => true]);
    }

    public function toggleArtist(Artist $artist): JsonResponse
    {
        $followed = FollowedArtist::where('user_id', auth()->id())
            ->where('artist_id', $artist->id)
            ->first();

        if ($followed) {
            $followed->delete();

            return response()->json(['following' => false]);
        }

        FollowedArtist::create([
            'user_id' => auth()->id(),
            'artist_id' => $artist->id,
        ]);

        return response()->json(['following' => true]);
    }

    public function togglePlaylistSong(Playlist $playlist, Song $song): JsonResponse
    {
        $this->authorize('update', $playlist);

        $pivot = $playlist->songs()->where('songs.id', $song->id)->first();

        if ($pivot) {
            $playlist->songs()->detach($song->id);

            return response()->json(['in_playlist' => false]);
        }

        $position = (int) $playlist->songs()->count() + 1;

        $playlist->songs()->attach($song->id, [
            'position' => $position,
            'added_by' => auth()->id(),
        ]);

        return response()->json(['in_playlist' => true]);
    }
}
