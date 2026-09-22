<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use App\Policies\Concerns\ChecksRole;

class StudentPolicy
{
    use ChecksRole;

    /**
     * Mahasiswa hanya boleh melihat barisnya sendiri.
     */
    public function view(User $user, Student $student): bool
    {
        if ($this->isMahasiswa($user)) {
            return $student->user_id === $user->id;
        }

        if ($this->isKaprodi($user)) {
            return $student->study_program_id === $this->studyProgramId($user);
        }

        return $this->isPimpinan($user);
    }

    /**
     * Hanya pemilik baris yang boleh mengubah biodatanya.
     *
     * Field akademik tidak dilindungi di sini melainkan di DTO/Service, karena
     * Policy hanya menjawab "baris siapa", bukan "kolom mana".
     */
    public function update(User $user, Student $student): bool
    {
        return $this->isMahasiswa($user) && $student->user_id === $user->id;
    }
}
