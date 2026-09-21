<?php

namespace App\Repositories\Eloquent;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Repositories\Contracts\AcademicPeriodRepository as AcademicPeriodRepositoryContract;
use Illuminate\Support\Collection;

class EloquentAcademicPeriodRepository implements AcademicPeriodRepositoryContract
{
    public function listAcademicYears(): Collection
    {
        return AcademicYear::query()
            ->select(['id', 'code', 'name', 'start_date', 'end_date', 'is_active'])
            ->with('semesters:id,academic_year_id,type,start_date,end_date,is_active')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function listSemesters(?int $academicYearId = null): Collection
    {
        $query = Semester::query()
            ->select(['id', 'academic_year_id', 'type', 'start_date', 'end_date', 'is_active'])
            ->with('academicYear:id,code,name');

        if ($academicYearId !== null) {
            $query->where('academic_year_id', $academicYearId);
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    public function createAcademicYear(array $data): AcademicYear
    {
        return AcademicYear::query()->create($data);
    }

    public function updateAcademicYear(int $id, array $data): AcademicYear
    {
        $academicYear = AcademicYear::query()->findOrFail($id);
        $academicYear->update($data);

        return $academicYear->fresh();
    }

    public function deleteAcademicYear(int $id): bool
    {
        return AcademicYear::query()->where('id', $id)->delete();
    }

    public function findAcademicYearById(int $id): ?AcademicYear
    {
        return AcademicYear::query()
            ->select(['id', 'code', 'name', 'start_date', 'end_date', 'is_active'])
            ->where('id', $id)
            ->first();
    }

    public function createSemester(array $data): Semester
    {
        return Semester::query()->create($data);
    }

    public function updateSemester(int $id, array $data): Semester
    {
        $semester = Semester::query()->findOrFail($id);
        $semester->update($data);

        return $semester->fresh();
    }

    public function deleteSemester(int $id): bool
    {
        return Semester::query()->where('id', $id)->delete();
    }

    public function findSemesterById(int $id): ?Semester
    {
        return Semester::query()
            ->select(['id', 'academic_year_id', 'type', 'start_date', 'end_date', 'is_active'])
            ->where('id', $id)
            ->first();
    }

    public function setActiveSemester(int $semesterId): void
    {
        Semester::query()->where('is_active', true)->update(['is_active' => false]);
        Semester::query()->where('id', $semesterId)->update(['is_active' => true]);
    }

    public function setActiveAcademicYear(int $academicYearId): void
    {
        AcademicYear::query()->where('is_active', true)->update(['is_active' => false]);
        AcademicYear::query()->where('id', $academicYearId)->update(['is_active' => true]);
    }
}
