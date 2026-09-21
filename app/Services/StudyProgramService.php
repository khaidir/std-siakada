<?php

namespace App\Services;

use App\DTO\StudyProgramData;
use App\Models\StudyProgram;
use App\Repositories\Contracts\FacultyRepository;
use App\Repositories\Contracts\StudyProgramRepository;
use Illuminate\Validation\ValidationException;

class StudyProgramService
{
    public function __construct(
        private readonly StudyProgramRepository $studyPrograms,
        private readonly FacultyRepository $faculties,
    ) {}

    /**
     * Data halaman daftar program studi.
     *
     * @return array<string, mixed>
     */
    public function pageData(): array
    {
        $items = $this->studyPrograms->listAll()
            ->map(fn (StudyProgram $sp) => [
                'id' => $sp->id,
                'faculty_id' => $sp->faculty_id,
                'faculty_name' => $sp->faculty?->name ?? '',
                'code' => $sp->code,
                'name' => $sp->name,
                'degree_level' => $sp->degree_level?->value ?? $sp->degree_level,
            ])
            ->values()
            ->all();

        $faculties = $this->faculties->listAll()
            ->map(fn ($f) => ['id' => $f->id, 'name' => $f->name])
            ->values()
            ->all();

        return [
            'study_programs' => $items,
            'faculties' => $faculties,
        ];
    }

    public function create(StudyProgramData $data): void
    {
        $existing = $this->studyPrograms->findByCode($data->code);

        if ($existing) {
            throw ValidationException::withMessages([
                'code' => 'Kode program studi sudah digunakan.',
            ]);
        }

        $this->studyPrograms->create([
            'faculty_id' => $data->faculty_id,
            'code' => $data->code,
            'name' => $data->name,
            'degree_level' => $data->degree_level,
        ]);
    }

    public function update(int $id, StudyProgramData $data): void
    {
        $existing = $this->studyPrograms->findByCode($data->code);

        if ($existing && $existing->id !== $id) {
            throw ValidationException::withMessages([
                'code' => 'Kode program studi sudah digunakan.',
            ]);
        }

        $this->studyPrograms->update($id, [
            'faculty_id' => $data->faculty_id,
            'code' => $data->code,
            'name' => $data->name,
            'degree_level' => $data->degree_level,
        ]);
    }

    public function delete(int $id): void
    {
        $this->studyPrograms->delete($id);
    }
}
