<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = [
        'name',
        'description',
        'song_id',
    ];
    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
