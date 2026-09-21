<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class GradePolicy
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

    public function view(User $user, Grade $grade): bool
    {
        if ($this->isSuperAdmin($user) || $this->isPimpinan($user)) {
            return true;
        }

        if ($this->isMahasiswa($user)) {
            return $grade->student_id === $this->studentId($user);
        }

        if ($this->isDosen($user)) {
            return $grade->courseOffering?->lecturer_id === $this->lecturerId($user);
        }

        if ($this->isKaprodi($user)) {
            return $grade->student?->study_program_id === $this->studyProgramId($user);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isDosen($user);
    }

    public function update(User $user, Grade $grade): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isDosen($user)
            && $grade->courseOffering?->lecturer_id === $this->lecturerId($user);
    }

    public function delete(User $user, Grade $grade): bool
    {
        return $this->isSuperAdmin($user);
    }
}
