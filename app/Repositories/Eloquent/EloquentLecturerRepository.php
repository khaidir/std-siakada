<?php

namespace App\Repositories\Eloquent;

use App\Models\Lecturer;
use App\Repositories\Contracts\LecturerRepository as LecturerRepositoryContract;

class EloquentLecturerRepository implements LecturerRepositoryContract
{
    public function create(array $data): Lecturer
    {
        return Lecturer::query()->create($data);
    }

    public function updateByUserId(int $userId, array $data): bool
    {
        return (bool) Lecturer::query()->where('user_id', $userId)->update($data);
    }

    public function findByUserId(int $userId): ?Lecturer
    {
        return Lecturer::query()
            ->select(['id', 'user_id', 'nidn', 'study_program_id', 'academic_rank'])
            ->where('user_id', $userId)
            ->first();
    }
}
