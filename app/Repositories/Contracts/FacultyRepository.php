<?php

namespace App\Repositories\Contracts;

use App\Models\Faculty;
use Illuminate\Support\Collection;

interface FacultyRepository
{
    /**
     * @return Collection<int, \App\Models\Faculty>
     */
    public function listAll(): Collection;

    public function create(array $data): Faculty;

    public function update(int $id, array $data): Faculty;

    public function delete(int $id): bool;

    public function findByCode(string $code): ?Faculty;

    public function findById(int $id): ?Faculty;
}
