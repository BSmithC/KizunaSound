<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Song extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'gender_id',
        'artist_id',
        'artist_ref_id',
        'name',
        'thumbnail',
        'lyrics',
        'audio',
        'duration_seconds',
        'release_date',
        'is_public',
        'play_count',
        'license',
        'mood',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'duration_seconds' => 'integer',
        'play_count' => 'integer',
        'release_date' => 'date',
    ];

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'gender_id');
    }

    public function gender()
    {
        return $this->genre();
    }

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_ref_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function albums()
    {
        return $this->belongsToMany(Album::class, 'album_song')
            ->using(AlbumSong::class)
            ->withPivot('position')
            ->orderBy('position');
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_song')
            ->using(PlaylistSong::class)
            ->withPivot(['position', 'added_by'])
            ->orderBy('position');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function listeningHistory()
    {
        return $this->hasMany(ListeningHistory::class);
    }

    public function playbackEvents()
    {
        return $this->hasMany(PlaybackEvent::class);
    }
}
