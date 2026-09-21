<?php

namespace App\Repositories\Contracts;

use App\Models\LecturerAttendance;
use Illuminate\Support\Collection;

interface LecturerAttendanceRepository
{
    /**
     * Daftar kehadiran mengajar dosen.
     *
     * @return Collection<int, LecturerAttendance>
     */
    public function listForLecturer(int $lecturerId): Collection;

    /**
     * Cari record kehadiran berdasarkan offering + tanggal.
     */
    public function findByOfferingAndDate(int $offeringId, string $date): ?LecturerAttendance;

    /**
     * Buat record kehadiran baru.
     */
    public function create(array $data): LecturerAttendance;

    /**
     * Update record kehadiran.
     */
    public function update(int $id, array $data): void;

    /**
     * Rekap kehadiran per dosen dengan filter.
     *
     * @param  array{study_program_id?: int, semester_id?: int, lecturer_id?: int}  $filters
     * @return Collection<int, LecturerAttendance>
     */
    public function summary(array $filters): Collection;

    /**
     * Detail riwayat kehadiran seorang dosen pada semester tertentu.
     *
     * @return Collection<int, LecturerAttendance>
     */
    public function detail(int $lecturerId, int $semesterId): Collection;
}
