<?php

namespace App\Policies;

use App\Models\StudyPlan;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class StudyPlanPolicy
{
    use ChecksRole;

    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user)
            || $this->isKaprodi($user)
            || $this->isDosen($user)
            || $this->isMahasiswa($user);
    }

    public function view(User $user, StudyPlan $plan): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        if ($this->isMahasiswa($user)) {
            return $plan->student_id === $this->studentId($user);
        }

        // Kaprodi (prodi-nya) & dosen PA (mahasiswa satu prodi).
        return ($this->isKaprodi($user) || $this->isDosen($user))
            && $plan->student?->study_program_id === $this->studyProgramId($user);
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isMahasiswa($user);
    }

    public function update(User $user, StudyPlan $plan): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isMahasiswa($user) && $plan->student_id === $this->studentId($user);
    }

    public function delete(User $user, StudyPlan $plan): bool
    {
        return $this->update($user, $plan);
    }

    /**
     * Approve/reject KRS oleh dosen PA (mahasiswa satu prodi).
     */
    public function approve(User $user, StudyPlan $plan): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isDosen($user)
            && $plan->student?->study_program_id === $this->studyProgramId($user);
    }
}
