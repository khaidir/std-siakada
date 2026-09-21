<?php

namespace App\Repositories\Contracts;

use App\Models\StudyProgram;
use Illuminate\Support\Collection;

interface StudyProgramRepository
{
    /**
     * @return Collection<int, \App\Models\StudyProgram>
     */
    public function listAll(): Collection;

    public function create(array $data): StudyProgram;

    public function update(int $id, array $data): StudyProgram;

    public function delete(int $id): bool;

    public function findByCode(string $code): ?StudyProgram;

    public function findById(int $id): ?StudyProgram;
}
