<?php

namespace App\Policies;

use App\Models\StudyPlanDetail;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class StudyPlanDetailPolicy
{
    use ChecksRole;

    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user)
            || $this->isKaprodi($user)
            || $this->isDosen($user)
            || $this->isMahasiswa($user);
    }

    public function view(User $user, StudyPlanDetail $detail): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        if ($this->isMahasiswa($user)) {
            return $detail->studyPlan?->student_id === $this->studentId($user);
        }

        return ($this->isKaprodi($user) || $this->isDosen($user))
            && $detail->studyPlan?->student?->study_program_id === $this->studyProgramId($user);
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isMahasiswa($user);
    }

    public function update(User $user, StudyPlanDetail $detail): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isMahasiswa($user)
            && $detail->studyPlan?->student_id === $this->studentId($user);
    }

    public function delete(User $user, StudyPlanDetail $detail): bool
    {
        return $this->update($user, $detail);
    }
}
