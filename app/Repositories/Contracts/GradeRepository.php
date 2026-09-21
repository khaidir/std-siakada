<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface GradeRepository
{
    /**
     * @return Collection<int, \App\Models\Grade>
     */
    public function listByOffering(int $offeringId): Collection;

    /**
     * Daftar nilai untuk seorang mahasiswa.
     *
     * @return Collection<int, \App\Models\Grade>
     */
    public function listForStudent(int $studentId): Collection;

    /**
     * Daftar nilai untuk seorang mahasiswa dalam semester tertentu.
     *
     * @return Collection<int, \App\Models\Grade>
     */
    public function listForStudentBySemester(int $studentId, int $semesterId): Collection;

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function upsertBatch(array $rows): void;
}
