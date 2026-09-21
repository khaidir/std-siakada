<?php

namespace App\Policies;

use App\Models\CourseOffering;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class CourseOfferingPolicy
{
    use ChecksRole;

    public function viewAny(User $user): bool
    {
        // Semua peran terautentikasi dapat melihat daftar (scoping di repository).
        return true;
    }

    public function view(User $user, CourseOffering $offering): bool
    {
        if ($this->isSuperAdmin($user) || $this->isPimpinan($user)) {
            return true;
        }

        if ($this->isKaprodi($user) || $this->isMahasiswa($user)) {
            return $offering->course?->study_program_id === $this->studyProgramId($user);
        }

        if ($this->isDosen($user)) {
            return $offering->lecturer_id === $this->lecturerId($user);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function update(User $user, CourseOffering $offering): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function delete(User $user, CourseOffering $offering): bool
    {
        return $this->isSuperAdmin($user);
    }
}
