<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use Illuminate\Support\Collection;

interface CourseRepository
{
    /**
     * Daftar mata kuliah dalam suatu program studi.
     *
     * @return Collection<int, \App\Models\Course>
     */
    public function listForStudyProgram(int $studyProgramId): Collection;

    public function create(array $data): Course;

    public function update(int $id, array $data): Course;

    public function delete(int $id): bool;

    /**
     * Cari berdasarkan kode (untuk validasi keunikan).
     */
    public function findByCode(string $code): ?Course;
}
