<?php

namespace App\Repositories\Contracts;

use App\Enums\InternshipStatus;
use App\Models\Internship;
use App\Models\InternshipLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface InternshipRepository
{
    /**
     * Cari KP milik mahasiswa.
     */
    public function findForStudent(int $studentId): ?Internship;

    /**
     * Daftar logbook KP.
     */
    public function listLogs(int $internshipId): Collection;

    /**
     * Tambah logbook baru.
     */
    public function addLog(array $data): InternshipLog;

    /**
     * Daftar semua KP (admin) dengan pagination.
     */
    public function listAll(array $filters): LengthAwarePaginator;

    /**
     * Detail KP dengan eager-load relasi.
     */
    public function detail(int $internshipId): ?Internship;

    /**
     * Assign dosen pembimbing KP.
     */
    public function assignSupervisor(int $internshipId, int $lecturerId): void;

    /**
     * Update status KP.
     */
    public function updateStatus(int $internshipId, InternshipStatus $status): void;
}
