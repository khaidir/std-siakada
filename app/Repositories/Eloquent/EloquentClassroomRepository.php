<?php

namespace App\Repositories\Eloquent;

use App\Models\Classroom;
use App\Repositories\Contracts\ClassroomRepository as ClassroomRepositoryContract;
use Illuminate\Support\Collection;

class EloquentClassroomRepository implements ClassroomRepositoryContract
{
    public function listAll(): Collection
    {
        return Classroom::query()
            ->select(['id', 'code', 'name', 'capacity', 'building'])
            ->orderBy('code')
            ->get();
    }

    public function create(array $data): Classroom
    {
        return Classroom::query()->create($data);
    }

    public function update(int $id, array $data): Classroom
    {
        $classroom = Classroom::query()->findOrFail($id);
        $classroom->update($data);

        return $classroom->fresh();
    }

    public function delete(int $id): bool
    {
        return Classroom::query()->where('id', $id)->delete();
    }

    public function findByCode(string $code): ?Classroom
    {
        return Classroom::query()
            ->select(['id', 'code', 'name', 'capacity', 'building'])
            ->where('code', $code)
            ->first();
    }

    public function findById(int $id): ?Classroom
    {
        return Classroom::query()
            ->select(['id', 'code', 'name', 'capacity', 'building'])
            ->where('id', $id)
            ->first();
    }
}
