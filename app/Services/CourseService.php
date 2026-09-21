<?php

namespace App\Services;

use App\DTO\CourseData;
use App\Models\Course;
use App\Models\User;
use App\Repositories\Contracts\CourseRepository;
use Illuminate\Validation\ValidationException;

class CourseService
{
    public function __construct(
        private readonly CourseRepository $courses,
    ) {}

    /**
     * Data halaman kelola mata kuliah untuk kaprodi.
     *
     * @return array<string, mixed>
     */
    public function pageData(User $user): array
    {
        $studyProgramId = $user->lecturer?->study_program_id;

        if (! $studyProgramId) {
            return ['courses' => []];
        }

        $courses = $this->courses->listForStudyProgram($studyProgramId);

        $items = $courses->map(function (Course $course) {
            return [
                'id' => $course->id,
                'code' => $course->code,
                'name' => $course->name,
                'sks' => $course->sks,
                'semester' => $course->semester,
                'type' => $course->type?->value ?? $course->type,
            ];
        })->values()->all();

        return [
            'courses' => $items,
        ];
    }

    /**
     * Buat mata kuliah baru.
     */
    public function create(User $user, CourseData $data): void
    {
        $studyProgramId = $user->lecturer?->study_program_id;

        if (! $studyProgramId) {
            throw ValidationException::withMessages([
                'study_program' => 'Anda tidak memiliki program studi.',
            ]);
        }

        // Cek keunikan kode.
        $existing = $this->courses->findByCode($data->code);

        if ($existing) {
            throw ValidationException::withMessages([
                'code' => 'Kode mata kuliah sudah digunakan.',
            ]);
        }

        $this->courses->create([
            'study_program_id' => $studyProgramId,
            'code' => $data->code,
            'name' => $data->name,
            'sks' => $data->sks,
            'semester' => $data->semester,
            'type' => $data->type,
        ]);
    }

    /**
     * Update mata kuliah.
     */
    public function update(User $user, int $id, CourseData $data): void
    {
        $course = Course::query()->select(['id', 'study_program_id', 'code'])->findOrFail($id);

        // Cek kepemilikan prodi.
        if ($course->study_program_id !== $user->lecturer?->study_program_id) {
            throw ValidationException::withMessages([
                'course' => 'Anda tidak memiliki akses ke mata kuliah ini.',
            ]);
        }

        // Cek keunikan kode (kecuali kode sendiri).
        $existing = $this->courses->findByCode($data->code);

        if ($existing && $existing->id !== $id) {
            throw ValidationException::withMessages([
                'code' => 'Kode mata kuliah sudah digunakan.',
            ]);
        }

        $this->courses->update($id, [
            'code' => $data->code,
            'name' => $data->name,
            'sks' => $data->sks,
            'semester' => $data->semester,
            'type' => $data->type,
        ]);
    }

    /**
     * Hapus mata kuliah.
     */
    public function delete(User $user, int $id): void
    {
        $course = Course::query()->select(['id', 'study_program_id'])->findOrFail($id);

        if ($course->study_program_id !== $user->lecturer?->study_program_id) {
            throw ValidationException::withMessages([
                'course' => 'Anda tidak memiliki akses ke mata kuliah ini.',
            ]);
        }

        $this->courses->delete($id);
    }
}
