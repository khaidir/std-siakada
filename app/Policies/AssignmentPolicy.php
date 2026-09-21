<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class AssignmentPolicy
{
    use ChecksRole;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Assignment $assignment): bool
    {
        if ($this->isSuperAdmin($user) || $this->isPimpinan($user)) {
            return true;
        }

        if ($this->isDosen($user)) {
            return $assignment->courseOffering?->lecturer_id === $this->lecturerId($user);
        }

        if ($this->isMahasiswa($user)) {
            return $assignment->courseOffering?->studyPlanDetails()
                ->whereHas('studyPlan', fn ($q) => $q->where('student_id', $this->studentId($user)))
                ->exists();
        }

        if ($this->isKaprodi($user)) {
            return $assignment->courseOffering?->course?->study_program_id === $this->studyProgramId($user);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isDosen($user);
    }

    public function update(User $user, Assignment $assignment): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->isDosen($user)
            && $assignment->courseOffering?->lecturer_id === $this->lecturerId($user);
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $this->update($user, $assignment);
    }
}
