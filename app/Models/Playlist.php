<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Playlist extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'img',
        'song_id',
        'user_id',
        'is_public',
        'is_collaborative',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_collaborative' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function songs()
    {
        return $this->belongsToMany(Song::class, 'playlist_song')
            ->using(PlaylistSong::class)
            ->withPivot(['position', 'added_by'])
            ->orderBy('position');
    }
}
