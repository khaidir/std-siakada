<?php

namespace App\Services\Admin;

use App\DTO\CourseData;
use App\Models\Course;
use App\Repositories\Contracts\CourseRepository;
use Illuminate\Validation\ValidationException;

/**
 * Service untuk admin — semua mata kuliah dari seluruh program studi.
 */
class AdminCourseService
{
    public function __construct(
        private readonly CourseRepository $courses,
    ) {}

    /**
     * Data halaman daftar mata kuliah (semua prodi).
     *
     * @return array<string, mixed>
     */
    public function pageData(): array
    {
        $all = \App\Models\Course::query()
            ->select(['id', 'study_program_id', 'code', 'name', 'sks', 'semester', 'type'])
            ->with('studyProgram:id,code,name')
            ->orderBy('study_program_id')
            ->orderBy('semester')
            ->orderBy('code')
            ->get();

        $items = $all->map(fn (Course $course) => [
            'id' => $course->id,
            'study_program_id' => $course->study_program_id,
            'study_program_name' => $course->studyProgram?->name ?? '',
            'code' => $course->code,
            'name' => $course->name,
            'sks' => $course->sks,
            'semester' => $course->semester,
            'type' => $course->type?->value ?? $course->type,
        ])->values()->all();

        $studyPrograms = \App\Models\StudyProgram::query()
            ->select(['id', 'code', 'name'])
            ->orderBy('name')
            ->get()
            ->map(fn ($sp) => ['id' => $sp->id, 'name' => "{$sp->code} - {$sp->name}"])
            ->values()
            ->all();

        return [
            'courses' => $items,
            'study_programs' => $studyPrograms,
        ];
    }

    public function create(CourseData $data): void
    {
        $existing = $this->courses->findByCode($data->code);

        if ($existing) {
            throw ValidationException::withMessages([
                'code' => 'Kode mata kuliah sudah digunakan.',
            ]);
        }

        $this->courses->create([
            'study_program_id' => request()->input('study_program_id'),
            'code' => $data->code,
            'name' => $data->name,
            'sks' => $data->sks,
            'semester' => $data->semester,
            'type' => $data->type,
        ]);
    }

    public function update(int $id, CourseData $data): void
    {
        $existing = $this->courses->findByCode($data->code);

        if ($existing && $existing->id !== $id) {
            throw ValidationException::withMessages([
                'code' => 'Kode mata kuliah sudah digunakan.',
            ]);
        }

        $this->courses->update($id, [
            'study_program_id' => request()->input('study_program_id'),
            'code' => $data->code,
            'name' => $data->name,
            'sks' => $data->sks,
            'semester' => $data->semester,
            'type' => $data->type,
        ]);
    }

    public function delete(int $id): void
    {
        $this->courses->delete($id);
    }
}
