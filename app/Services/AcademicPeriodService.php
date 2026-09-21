<?php

namespace App\Services;

use App\DTO\AcademicYearData;
use App\DTO\SemesterData;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Repositories\Contracts\AcademicPeriodRepository;
use Illuminate\Validation\ValidationException;

class AcademicPeriodService
{
    public function __construct(
        private readonly AcademicPeriodRepository $periods,
    ) {}

    /**
     * Data halaman periode akademik.
     *
     * @return array<string, mixed>
     */
    public function pageData(): array
    {
        $academicYears = $this->periods->listAcademicYears()
            ->map(fn (AcademicYear $ay) => [
                'id' => $ay->id,
                'code' => $ay->code,
                'name' => $ay->name,
                'start_date' => $ay->start_date?->format('Y-m-d'),
                'end_date' => $ay->end_date?->format('Y-m-d'),
                'is_active' => $ay->is_active,
                'semesters' => $ay->semesters->map(fn (Semester $s) => [
                    'id' => $s->id,
                    'academic_year_id' => $s->academic_year_id,
                    'type' => $s->type?->value ?? $s->type,
                    'start_date' => $s->start_date?->format('Y-m-d'),
                    'end_date' => $s->end_date?->format('Y-m-d'),
                    'is_active' => $s->is_active,
                ])->values()->all(),
            ])
            ->values()
            ->all();

        return ['academic_years' => $academicYears];
    }

    public function createAcademicYear(AcademicYearData $data): void
    {
        $this->periods->createAcademicYear([
            'code' => $data->code,
            'name' => $data->name,
            'start_date' => $data->start_date,
            'end_date' => $data->end_date,
            'is_active' => $data->is_active,
        ]);

        if ($data->is_active) {
            $latest = $this->periods->listAcademicYears()->first();
            if ($latest) {
                $this->periods->setActiveAcademicYear($latest->id);
            }
        }
    }

    public function updateAcademicYear(int $id, AcademicYearData $data): void
    {
        $this->periods->updateAcademicYear($id, [
            'code' => $data->code,
            'name' => $data->name,
            'start_date' => $data->start_date,
            'end_date' => $data->end_date,
            'is_active' => $data->is_active,
        ]);

        if ($data->is_active) {
            $this->periods->setActiveAcademicYear($id);
        }
    }

    public function deleteAcademicYear(int $id): void
    {
        $this->periods->deleteAcademicYear($id);
    }

    public function createSemester(SemesterData $data): void
    {
        $this->periods->createSemester([
            'academic_year_id' => $data->academic_year_id,
            'type' => $data->type,
            'start_date' => $data->start_date,
            'end_date' => $data->end_date,
            'is_active' => $data->is_active,
        ]);

        if ($data->is_active) {
            $latest = $this->periods->listSemesters($data->academic_year_id)->first();
            if ($latest) {
                $this->periods->setActiveSemester($latest->id);
            }
        }
    }

    public function updateSemester(int $id, SemesterData $data): void
    {
        $this->periods->updateSemester($id, [
            'academic_year_id' => $data->academic_year_id,
            'type' => $data->type,
            'start_date' => $data->start_date,
            'end_date' => $data->end_date,
            'is_active' => $data->is_active,
        ]);

        if ($data->is_active) {
            $this->periods->setActiveSemester($id);
        }
    }

    public function deleteSemester(int $id): void
    {
        $this->periods->deleteSemester($id);
    }
}
