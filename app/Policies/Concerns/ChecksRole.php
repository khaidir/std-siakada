<?php

namespace App\Policies\Concerns;

use App\Models\User;

/**
 * Helper pemeriksaan peran & profil pengguna untuk Policy row-level.
 *
 * Semua akses super-admin sudah ditangani di Gate::before (return true),
 * sehingga Policy hanya perlu mengkodekan aturan untuk peran non-super-admin.
 */
trait ChecksRole
{
    protected function isSuperAdmin(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    protected function isKaprodi(User $user): bool
    {
        return $user->hasRole('kaprodi');
    }

    protected function isDosen(User $user): bool
    {
        return $user->hasRole('dosen');
    }

    protected function isMahasiswa(User $user): bool
    {
        return $user->hasRole('mahasiswa');
    }

    protected function isPimpinan(User $user): bool
    {
        return $user->hasRole('pimpinan');
    }

    /**
     * ID dosen pengguna (jika memiliki profil Lecturer).
     */
    protected function lecturerId(User $user): ?int
    {
        return $user->lecturer?->id;
    }

    /**
     * ID mahasiswa pengguna (jika memiliki profil Student).
     */
    protected function studentId(User $user): ?int
    {
        return $user->student?->id;
    }

    /**
     * ID program studi pengguna (dari profil Lecturer/Student).
     */
    protected function studyProgramId(User $user): ?int
    {
        return $user->lecturer?->study_program_id ?? $user->student?->study_program_id;
    }
}
