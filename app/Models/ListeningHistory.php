<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListeningHistory extends Model
{
    protected $table = 'listening_history';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'song_id',
        'listened_at',
        'source',
    ];

    protected $casts = [
        'listened_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
