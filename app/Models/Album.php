<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Album extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'img',
        'license',
        'song_id',
        'artists',
        'artist_id',
        'user_id',
        'release_date',
        'is_public',
    ];

    protected $casts = [
        'artists' => 'array',
        'is_public' => 'boolean',
        'release_date' => 'date',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function songs()
    {
        return $this->belongsToMany(Song::class, 'album_song')
            ->using(AlbumSong::class)
            ->withPivot('position')
            ->orderBy('position');
    }

    public function savedBy()
    {
        return $this->hasMany(SavedAlbum::class);
    }
}
