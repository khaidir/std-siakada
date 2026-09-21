<?php

namespace App\Repositories\Contracts;

use App\Enums\ThesisStatus;
use App\Models\Thesis;
use App\Models\ThesisLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ThesisRepository
{
    /**
     * Cari skripsi milik mahasiswa.
     */
    public function findForStudent(int $studentId): ?Thesis;

    /**
     * Daftar skripsi yang dibimbing oleh dosen.
     *
     * @return Collection<int, Thesis>
     */
    public function listForSupervisor(int $lecturerId): Collection;

    /**
     * Detail skripsi dengan eager-load relasi.
     */
    public function detail(int $thesisId): ?Thesis;

    /**
     * Daftar log bimbingan skripsi.
     */
    public function listLogs(int $thesisId): Collection;

    /**
     * Tambah log bimbingan baru.
     */
    public function addLog(array $data): ThesisLog;

    /**
     * Update status skripsi.
     */
    public function updateStatus(int $thesisId, ThesisStatus $status): void;

    /**
     * Setujui log bimbingan.
     */
    public function approveLog(int $logId): void;

    /**
     * Daftar semua skripsi (admin) dengan pagination.
     */
    public function listAll(array $filters): LengthAwarePaginator;

    /**
     * Assign pembimbing 1 & 2.
     */
    public function assignSupervisors(int $thesisId, int $supervisor1Id, ?int $supervisor2Id): void;
}
