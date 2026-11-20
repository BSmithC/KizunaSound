<?php

namespace App\Policies;

use App\Models\User;

class GenderPolicy
{
    /**
     * La taxonomía es compartida: cualquier usuario autenticado puede
     * proponer géneros, pero no se ha asignado un rol admin automáticamente.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user): bool
    {
        return true;
    }

    public function delete(User $user): bool
    {
        return true;
    }
}
