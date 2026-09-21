<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksRole;
use Illuminate\Database\Eloquent\Model;

/**
 * Policy untuk periode akademik (AcademicYear & Semester).
 *
 * Hanya super-admin yang kelola; peran lain read-only.
 * Didaftarkan secara eksplisit di AuthServiceProvider via Gate::policy.
 */
class AcademicPeriodPolicy
{
    use ChecksRole;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Model $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function update(User $user, Model $model): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->isSuperAdmin($user);
    }
}
