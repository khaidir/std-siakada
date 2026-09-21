<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class AttendancePolicy
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

    public function view(User $user, Attendance $attendance): bool
    {
        if ($this->isSuperAdmin($user) || $this->isPimpinan($user)) {
            return true;
        }

        if ($this->isMahasiswa($user)) {
            return $attendance->student_id === $this->studentId($user);
        }

        if ($this->isDosen($user)) {
            return $attendance->courseOffering?->lecturer_id === $this->lecturerId($user);
        }

        if ($this->isKaprodi($user)) {
            return $attendance->student?->study_program_id === $this->studyProgramId($user);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isDosen($user);
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isDosen($user)
            && $attendance->courseOffering?->lecturer_id === $this->lecturerId($user);
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $this->isSuperAdmin($user);
    }
}
