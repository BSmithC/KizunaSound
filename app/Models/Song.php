<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = [
        'gender_id',
        'artist_id',
        'name',
        'thumbnail',
        'lyrics',
        'audio',
    ];
    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }
    public function artist()
    {
        return $this->belongsTo(User::class);
    }

}
