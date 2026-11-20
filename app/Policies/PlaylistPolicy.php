<?php

namespace App\Policies;

use App\Models\Playlist;
use App\Models\User;

class PlaylistPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Playlist $playlist): bool
    {
        return $playlist->is_public || $playlist->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Playlist $playlist): bool
    {
        return $playlist->user_id === $user->id
            || $user->hasRole('admin')
            || $user->hasRole('editor');
    }

    public function delete(User $user, Playlist $playlist): bool
    {
        return $this->update($user, $playlist);
    }

    public function addSong(User $user, Playlist $playlist): bool
    {
        return $this->update($user, $playlist);
    }
}
