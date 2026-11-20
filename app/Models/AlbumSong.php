<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AlbumSong extends Pivot
{
    protected $table = 'album_song';

    public $incrementing = true;

    protected $fillable = [
        'album_id',
        'song_id',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
