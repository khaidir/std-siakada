<?php

namespace App\Repositories\Eloquent;

use App\Models\Faculty;
use App\Repositories\Contracts\FacultyRepository as FacultyRepositoryContract;
use Illuminate\Support\Collection;

class EloquentFacultyRepository implements FacultyRepositoryContract
{
    public function listAll(): Collection
    {
        return Faculty::query()
            ->select(['id', 'code', 'name'])
            ->orderBy('code')
            ->get();
    }

    public function create(array $data): Faculty
    {
        return Faculty::query()->create($data);
    }

    public function update(int $id, array $data): Faculty
    {
        $faculty = Faculty::query()->findOrFail($id);
        $faculty->update($data);

        return $faculty->fresh();
    }

    public function delete(int $id): bool
    {
        return Faculty::query()->where('id', $id)->delete();
    }

    public function findByCode(string $code): ?Faculty
    {
        return Faculty::query()
            ->select(['id', 'code', 'name'])
            ->where('code', $code)
            ->first();
    }

    public function findById(int $id): ?Faculty
    {
        return Faculty::query()
            ->select(['id', 'code', 'name'])
            ->where('id', $id)
            ->first();
    }
}
