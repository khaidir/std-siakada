<?php

namespace App\Policies;

use App\Models\Thesis;
use App\Models\ThesisLog;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class ThesisLogPolicy
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

    public function view(User $user, ThesisLog $log): bool
    {
        return $this->canViewThesis($user, $log->thesis);
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isMahasiswa($user);
    }

    public function update(User $user, ThesisLog $log): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isMahasiswa($user) && $log->thesis?->student_id === $this->studentId($user);
    }

    /**
     * Dosen pembimbing approve log bimbingan.
     */
    public function approve(User $user, ThesisLog $log): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        $thesis = $log->thesis;

        return $this->isDosen($user)
            && $thesis !== null
            && in_array($this->lecturerId($user), [$thesis->supervisor_1_id, $thesis->supervisor_2_id], true);
    }

    public function delete(User $user, ThesisLog $log): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isMahasiswa($user) && $log->thesis?->student_id === $this->studentId($user);
    }

    private function canViewThesis(User $user, ?Thesis $thesis): bool
    {
        if ($thesis === null) {
            return false;
        }

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
}
