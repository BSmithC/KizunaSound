<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artist extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'bio',
        'image',
        'cover',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function songs()
    {
        return $this->hasMany(Song::class, 'artist_ref_id');
    }

    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    public function followers()
    {
        return $this->hasMany(FollowedArtist::class);
    }
}
