<?php

namespace App\Repositories\Contracts;

interface StudentRepository
{
    public function recalculateGpaAndSks(int $studentId): void;

    public function create(array $data): \App\Models\Student;

    public function findByUserId(int $userId): ?\App\Models\Student;
}
