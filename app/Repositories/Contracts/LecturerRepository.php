<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface LecturerRepository
{
    public function create(array $data): \App\Models\Lecturer;

    public function updateByUserId(int $userId, array $data): bool;

    public function findByUserId(int $userId): ?\App\Models\Lecturer;
}
