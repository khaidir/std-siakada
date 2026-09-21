<?php

namespace App\Repositories\Contracts;

interface DashboardRepository
{
    /**
     * Statistik dashboard super-admin (global).
     *
     * @return array<string, mixed>
     */
    public function superAdminStats(): array;

    /**
     * Statistik dashboard kaprodi (scoped per program studi).
     *
     * @return array<string, mixed>
     */
    public function kaprodiStats(int $studyProgramId): array;

    /**
     * Statistik dashboard dosen (scoped per dosen pengampu).
     *
     * @return array<string, mixed>
     */
    public function dosenStats(int $lecturerId): array;

    /**
     * Statistik dashboard mahasiswa (scoped per mahasiswa).
     *
     * @return array<string, mixed>
     */
    public function mahasiswaStats(int $studentId): array;

    /**
     * Statistik dashboard pimpinan (read-only, agregat global).
     *
     * @return array<string, mixed>
     */
    public function pimpinanStats(): array;
}
