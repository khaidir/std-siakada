<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class UserPolicy
{
    use ChecksRole;

    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function view(User $user, User $model): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function update(User $user, User $model): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function delete(User $user, User $model): bool
    {
        return $this->isSuperAdmin($user);
    }
}
