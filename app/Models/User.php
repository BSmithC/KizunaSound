<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function artistProfile()
    {
        return $this->hasOne(Artist::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function followedArtists()
    {
        return $this->hasMany(FollowedArtist::class);
    }

    public function savedAlbums()
    {
        return $this->hasMany(SavedAlbum::class);
    }

    public function listeningHistory()
    {
        return $this->hasMany(ListeningHistory::class);
    }

    public function playbackEvents()
    {
        return $this->hasMany(PlaybackEvent::class);
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'user_id');
    }

    public function uploadedSongs()
    {
        return $this->hasMany(Song::class, 'artist_id');
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }
}
