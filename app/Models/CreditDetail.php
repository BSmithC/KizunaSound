<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditDetail extends Model
{
    protected $fillable = [
        'role',
        'user_id',
        'song_id',
    ];
}
