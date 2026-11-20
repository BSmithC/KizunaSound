<?php

namespace App\Policies;

use App\Models\Song;
use App\Models\User;

class SongPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Song $song): bool
    {
        return $song->is_public || $song->artist_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Song $song): bool
    {
        return $song->artist_id === $user->id
            || $user->hasRole('admin')
            || $user->hasRole('editor');
    }

    public function delete(User $user, Song $song): bool
    {
        return $this->update($user, $song);
    }
}
