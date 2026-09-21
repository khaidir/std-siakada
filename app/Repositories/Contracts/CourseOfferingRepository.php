<?php

namespace App\Repositories\Contracts;

use App\Models\CourseOffering;
use Illuminate\Support\Collection;

interface CourseOfferingRepository
{
    /**
     * Daftar kelas yang diampu seorang dosen.
     *
     * @return Collection<int, \App\Models\CourseOffering>
     */
    public function listByLecturer(int $lecturerId): Collection;

    /**
     * Daftar kelas yang tersedia untuk KRS mahasiswa pada semester aktif.
     * Hanya menampilkan kelas dari program studi mahasiswa.
     *
     * @return Collection<int, \App\Models\CourseOffering>
     */
    public function availableForStudent(int $studentId, int $semesterId): Collection;

    /**
     * Jadwal kuliah untuk mahasiswa pada semester tertentu (dari study_plan_details approved).
     *
     * @return Collection<int, \App\Models\CourseOffering>
     */
    public function scheduleForStudent(int $studentId, int $semesterId): Collection;

    /**
     * Daftar semua kelas (untuk super-admin).
     *
     * @return Collection<int, \App\Models\CourseOffering>
     */
    public function listAll(): Collection;

    public function create(array $data): CourseOffering;

    public function update(int $id, array $data): CourseOffering;

    public function delete(int $id): bool;

    public function findById(int $id): ?CourseOffering;
}
