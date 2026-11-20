<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PlaylistSong extends Pivot
{
    protected $table = 'playlist_song';

    public $incrementing = true;

    protected $fillable = [
        'playlist_id',
        'song_id',
        'position',
        'added_by',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
