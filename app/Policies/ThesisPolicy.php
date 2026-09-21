<?php

namespace App\Policies;

use App\Models\Thesis;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class ThesisPolicy
{
    use ChecksRole;

    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user)
            || $this->isKaprodi($user)
            || $this->isDosen($user)
            || $this->isMahasiswa($user)
            || $this->isPimpinan($user);
    }

    public function view(User $user, Thesis $thesis): bool
    {
        if ($this->isSuperAdmin($user) || $this->isPimpinan($user)) {
            return true;
        }

        if ($this->isMahasiswa($user)) {
            return $thesis->student_id === $this->studentId($user);
        }

        if ($this->isDosen($user)) {
            return in_array($this->lecturerId($user), [$thesis->supervisor_1_id, $thesis->supervisor_2_id], true);
        }

        if ($this->isKaprodi($user)) {
            return $thesis->student?->study_program_id === $this->studyProgramId($user);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isMahasiswa($user);
    }

    public function update(User $user, Thesis $thesis): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        if ($this->isMahasiswa($user)) {
            return $thesis->student_id === $this->studentId($user);
        }

        if ($this->isDosen($user)) {
            return in_array($this->lecturerId($user), [$thesis->supervisor_1_id, $thesis->supervisor_2_id], true);
        }

        return false;
    }

    public function delete(User $user, Thesis $thesis): bool
    {
        return $this->isSuperAdmin($user);
    }

    /**
     * Admin assign pembimbing skripsi.
     */
    public function assignSupervisor(User $user, Thesis $thesis): bool
    {
        return $this->isSuperAdmin($user);
    }
}
