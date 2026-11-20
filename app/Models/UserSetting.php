<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'theme',
        'accent_color',
        'playback_quality',
        'autoplay',
        'normalize_volume',
        'volume',
        'shuffle',
        'repeat',
        'language',
        'privacy',
        'notifications',
        'audio_preferences',
    ];

    protected $casts = [
        'autoplay' => 'boolean',
        'normalize_volume' => 'boolean',
        'volume' => 'float',
        'shuffle' => 'boolean',
        'notifications' => 'boolean',
        'audio_preferences' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
