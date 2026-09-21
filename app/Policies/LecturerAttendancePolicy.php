<?php

namespace App\Policies;

use App\Models\LecturerAttendance;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class LecturerAttendancePolicy
{
    use ChecksRole;

    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user)
            || $this->isKaprodi($user)
            || $this->isDosen($user)
            || $this->isPimpinan($user);
    }

    public function view(User $user, LecturerAttendance $attendance): bool
    {
        if ($this->isSuperAdmin($user) || $this->isPimpinan($user)) {
            return true;
        }

        if ($this->isDosen($user)) {
            return $attendance->lecturer_id === $this->lecturerId($user);
        }

        if ($this->isKaprodi($user)) {
            return $attendance->lecturer?->study_program_id === $this->studyProgramId($user);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isDosen($user);
    }

    public function update(User $user, LecturerAttendance $attendance): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isDosen($user) && $attendance->lecturer_id === $this->lecturerId($user);
    }

    public function delete(User $user, LecturerAttendance $attendance): bool
    {
        return $this->isSuperAdmin($user);
    }
}
