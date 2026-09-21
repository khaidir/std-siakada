<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class CoursePolicy
{
    use ChecksRole;

    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user) || $user->hasPermissionTo('courses.view');
    }

    public function view(User $user, Course $course): bool
    {
        if ($this->isSuperAdmin($user)
            || $this->isDosen($user)
            || $this->isMahasiswa($user)
            || $this->isPimpinan($user)) {
            return true;
        }

        return $this->isKaprodi($user)
            && $course->study_program_id === $this->studyProgramId($user);
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user) || $user->hasPermissionTo('courses.create');
    }

    public function update(User $user, Course $course): bool
    {
        return $this->isSuperAdmin($user)
            || ($this->isKaprodi($user) && $course->study_program_id === $this->studyProgramId($user));
    }

    public function delete(User $user, Course $course): bool
    {
        return $this->update($user, $course);
    }
}
