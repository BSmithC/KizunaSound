<?php

namespace App\Policies;

use App\Models\Album;
use App\Models\User;

class AlbumPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Album $album): bool
    {
        return $album->is_public || $album->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Album $album): bool
    {
        return $album->user_id === $user->id
            || $user->hasRole('admin')
            || $user->hasRole('editor');
    }

    public function delete(User $user, Album $album): bool
    {
        return $this->update($user, $album);
    }
}
