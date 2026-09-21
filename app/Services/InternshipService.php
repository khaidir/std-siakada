<?php

namespace App\Services;

use App\DTO\InternshipAssignData;
use App\DTO\InternshipLogData;
use App\Enums\InternshipStatus;
use App\Models\Internship;
use App\Models\InternshipLog;
use App\Repositories\Contracts\InternshipRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InternshipService
{
    public function __construct(
        protected InternshipRepository $internships,
    ) {}

    /**
     * Data halaman KP mahasiswa.
     *
     * @return array{internship: ?Internship, logs: Collection}
     */
    public function dataForStudent(int $studentId): array
    {
        $internship = $this->internships->findForStudent($studentId);

        $logs = collect();
        if ($internship) {
            $logs = $this->internships->listLogs((int) $internship->id);
        }

        return [
            'internship' => $internship,
            'logs' => $logs,
        ];
    }

    /**
     * Tambah logbook harian.
     */
    public function addLog(int $studentId, InternshipLogData $data): InternshipLog
    {
        $internship = $this->internships->findForStudent($studentId);

        if (! $internship) {
            abort(404, 'KP tidak ditemukan.');
        }

        if ((int) $internship->id !== $data->internship_id) {
            abort(403, 'Anda tidak memiliki akses ke KP ini.');
        }

        return $this->internships->addLog($data->toArray());
    }

    /**
     * Daftar semua KP (admin) dengan pagination.
     */
    public function listAll(array $filters): LengthAwarePaginator
    {
        return $this->internships->listAll($filters);
    }

    /**
     * Assign dosen pembimbing KP oleh admin.
     */
    public function assignSupervisor(InternshipAssignData $data): void
    {
        $this->internships->assignSupervisor(
            $data->internship_id,
            $data->supervisor_id,
        );
    }

    /**
     * Update status KP oleh admin.
     */
    public function adminUpdateStatus(int $internshipId, InternshipStatus $status): void
    {
        $this->internships->updateStatus($internshipId, $status);
    }
}
