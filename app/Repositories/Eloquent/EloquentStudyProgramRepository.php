<?php

namespace App\Repositories\Eloquent;

use App\Models\StudyProgram;
use App\Repositories\Contracts\StudyProgramRepository as StudyProgramRepositoryContract;
use Illuminate\Support\Collection;

class EloquentStudyProgramRepository implements StudyProgramRepositoryContract
{
    public function listAll(): Collection
    {
        return StudyProgram::query()
            ->select(['id', 'faculty_id', 'code', 'name', 'degree_level'])
            ->with('faculty:id,name')
            ->orderBy('code')
            ->get();
    }

    public function create(array $data): StudyProgram
    {
        return StudyProgram::query()->create($data);
    }

    public function update(int $id, array $data): StudyProgram
    {
        $studyProgram = StudyProgram::query()->findOrFail($id);
        $studyProgram->update($data);

        return $studyProgram->fresh();
    }

    public function delete(int $id): bool
    {
        return StudyProgram::query()->where('id', $id)->delete();
    }

    public function findByCode(string $code): ?StudyProgram
    {
        return StudyProgram::query()
            ->select(['id', 'faculty_id', 'code', 'name', 'degree_level'])
            ->where('code', $code)
            ->first();
    }

    public function findById(int $id): ?StudyProgram
    {
        return StudyProgram::query()
            ->select(['id', 'faculty_id', 'code', 'name', 'degree_level'])
            ->where('id', $id)
            ->first();
    }
}
