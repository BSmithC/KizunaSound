<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaybackEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'song_id',
        'artist_id',
        'album_id',
        'playlist_id',
        'event_type',
        'position_seconds',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
        'position_seconds' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }
}
