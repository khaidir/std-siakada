<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface AttendanceRepository
{
    /**
     * @return Collection<int, \App\Models\Attendance>
     */
    public function listByOffering(int $offeringId): Collection;

    /**
     * @return Collection<int, \App\Models\Attendance>
     */
    public function recapByOffering(int $offeringId): Collection;

    /**
     * Riwayat presensi mahasiswa pada suatu kelas.
     *
     * @return Collection<int, \App\Models\Attendance>
     */
    public function listForStudent(int $studentId, int $offeringId): Collection;

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function upsertBatch(array $rows): void;
}
