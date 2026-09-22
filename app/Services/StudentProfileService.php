<?php

namespace App\Services;

use App\DTO\StudentProfileData;
use App\Models\User;
use App\Repositories\Contracts\StudentRepository;
use Illuminate\Support\Facades\DB;

class StudentProfileService
{
    public function __construct(
        protected StudentRepository $students,
    ) {}

    /**
     * Data profil untuk ditampilkan di halaman Profile Settings.
     *
     * @return array<string, mixed>|null
     */
    public function profileFor(int $userId): ?array
    {
        return $this->students->profileForUser($userId);
    }

    /**
     * Simpan perubahan biodata.
     *
     * Hanya kolom yang ada di DTO yang ikut tersimpan, sehingga field akademik
     * (nim, gpa, total_sks, status, study_program_id, entry_year) tidak pernah
     * tersentuh walau dikirim di payload.
     */
    public function update(User $user, int $studentId, StudentProfileData $data): void
    {
        DB::transaction(function () use ($user, $studentId, $data): void {
            $user->forceFill([
                'name' => $data->name,
                'email' => $data->email,
            ])->save();

            $this->students->updateProfile($studentId, [
                'birth_place' => $data->birth_place,
                'birth_date' => $data->birth_date,
                'gender' => $data->gender,
                'address' => $data->address,
                'phone' => $data->phone,
            ]);
        });
    }
}
