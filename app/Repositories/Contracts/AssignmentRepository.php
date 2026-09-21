<?php

namespace App\Repositories\Contracts;

use App\Models\Assignment;
use Illuminate\Support\Collection;

interface AssignmentRepository
{
    /**
     * Daftar tugas untuk mahasiswa dari kelas yang diikuti.
     *
     * @return Collection<int, Assignment>
     */
    public function listForStudent(int $studentId): Collection;

    /**
     * Daftar tugas berdasarkan kelas (offering).
     *
     * @return Collection<int, Assignment>
     */
    public function listByOffering(int $offeringId): Collection;

    /**
     * Buat tugas baru.
     */
    public function create(array $data): Assignment;

    /**
     * Update tugas.
     */
    public function update(int $id, array $data): Assignment;

    /**
     * Hapus tugas.
     */
    public function delete(int $id): void;

    /**
     * Cari tugas berdasarkan ID.
     */
    public function findById(int $id): ?Assignment;
}
