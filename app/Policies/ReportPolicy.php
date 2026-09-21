<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksRole;

/**
 * Policy untuk laporan & dashboard pimpinan.
 *
 * Hanya pimpinan (super-admin, kaprodi) yang dapat melihat laporan.
 * Read-only: tidak ada endpoint mutasi data.
 */
class ReportPolicy
{
    use ChecksRole;

    public function view(User $user): bool
    {
        return $user->hasRole('super-admin') || $user->hasRole('kaprodi') || $user->hasRole('pimpinan');
    }
}
