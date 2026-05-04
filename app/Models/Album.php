<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = [
        'name',
        'license',
        'song_id',
        'artists',
    ];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
