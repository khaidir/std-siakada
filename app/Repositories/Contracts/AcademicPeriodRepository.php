<?php

namespace App\Repositories\Contracts;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Support\Collection;

interface AcademicPeriodRepository
{
    /**
     * @return Collection<int, \App\Models\AcademicYear>
     */
    public function listAcademicYears(): Collection;

    /**
     * @return Collection<int, \App\Models\Semester>
     */
    public function listSemesters(?int $academicYearId = null): Collection;

    public function createAcademicYear(array $data): AcademicYear;

    public function updateAcademicYear(int $id, array $data): AcademicYear;

    public function deleteAcademicYear(int $id): bool;

    public function findAcademicYearById(int $id): ?AcademicYear;

    public function createSemester(array $data): Semester;

    public function updateSemester(int $id, array $data): Semester;

    public function deleteSemester(int $id): bool;

    public function findSemesterById(int $id): ?Semester;

    /**
     * Nonaktifkan semua semester, lalu aktifkan satu.
     */
    public function setActiveSemester(int $semesterId): void;

    /**
     * Nonaktifkan semua tahun ajaran, lalu aktifkan satu.
     */
    public function setActiveAcademicYear(int $academicYearId): void;
}
