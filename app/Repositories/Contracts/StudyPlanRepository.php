<?php

namespace App\Repositories\Contracts;

use App\Models\StudyPlan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface StudyPlanRepository
{
    /**
     * Cari KRS aktif milik mahasiswa pada semester tertentu.
     */
    public function activeForStudent(int $studentId, int $semesterId): ?StudyPlan;

    /**
     * Buat KRS baru (draft) untuk mahasiswa pada semester tertentu.
     */
    public function createForStudent(int $studentId, int $semesterId): StudyPlan;

    /**
     * Simpan perubahan KRS.
     */
    public function save(StudyPlan $plan): void;

    /**
     * Daftar KRS submitted milik mahasiswa satu prodi (bimbingan PA).
     *
     * @return Collection<int, StudyPlan>
     */
    public function listForAdvisor(int $lecturerId): Collection;

    /**
     * Detail KRS dengan eager-load details + offering.course.
     */
    public function detail(int $planId): ?StudyPlan;

    /**
     * Daftar semua KRS (untuk admin) dengan filter dan pagination.
     *
     * @param array{fakultas?: int, prodi?: int, semester?: int, status?: string} $filters
     */
    public function listAll(array $filters = []): LengthAwarePaginator;
}
