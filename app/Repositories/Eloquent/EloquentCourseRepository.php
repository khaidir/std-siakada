<?php

namespace App\Repositories\Eloquent;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepository as CourseRepositoryContract;
use Illuminate\Support\Collection;

class EloquentCourseRepository implements CourseRepositoryContract
{
    public function listForStudyProgram(int $studyProgramId): Collection
    {
        return Course::query()
            ->select(['id', 'study_program_id', 'code', 'name', 'sks', 'semester', 'type'])
            ->where('study_program_id', $studyProgramId)
            ->orderBy('semester')
            ->orderBy('code')
            ->get();
    }

    public function create(array $data): Course
    {
        return Course::query()->create($data);
    }

    public function update(int $id, array $data): Course
    {
        $course = Course::query()->findOrFail($id);
        $course->update($data);

        return $course->fresh();
    }

    public function delete(int $id): bool
    {
        return Course::query()->where('id', $id)->delete();
    }

    public function findByCode(string $code): ?Course
    {
        return Course::query()
            ->select(['id', 'study_program_id', 'code'])
            ->where('code', $code)
            ->first();
    }
}
