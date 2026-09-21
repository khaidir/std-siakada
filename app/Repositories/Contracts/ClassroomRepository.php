<?php

namespace App\Repositories\Contracts;

use App\Models\Classroom;
use Illuminate\Support\Collection;

interface ClassroomRepository
{
    /**
     * @return Collection<int, \App\Models\Classroom>
     */
    public function listAll(): Collection;

    public function create(array $data): Classroom;

    public function update(int $id, array $data): Classroom;

    public function delete(int $id): bool;

    public function findByCode(string $code): ?Classroom;

    public function findById(int $id): ?Classroom;
}
