<?php

namespace App\Services;

use App\DTO\ThesisAssignData;
use App\DTO\ThesisLogData;
use App\Enums\ThesisStatus;
use App\Models\Thesis;
use App\Models\ThesisLog;
use App\Repositories\Contracts\ThesisRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ThesisService
{
    public function __construct(
        protected ThesisRepository $theses,
    ) {}

    /**
     * Data halaman skripsi mahasiswa.
     *
     * @return array{thesis: ?Thesis, logs: Collection}
     */
    public function dataForStudent(int $studentId): array
    {
        $thesis = $this->theses->findForStudent($studentId);

        $logs = collect();
        if ($thesis) {
            $logs = $this->theses->listLogs((int) $thesis->id);
        }

        return [
            'thesis' => $thesis,
            'logs' => $logs,
        ];
    }

    /**
     * Tambah log bimbingan.
     */
    public function addLog(int $studentId, ThesisLogData $data): ThesisLog
    {
        $thesis = $this->theses->findForStudent($studentId);

        if (! $thesis) {
            abort(404, 'Skripsi tidak ditemukan.');
        }

        if ((int) $thesis->id !== $data->thesis_id) {
            abort(403, 'Anda tidak memiliki akses ke skripsi ini.');
        }

        return $this->theses->addLog($data->toArray());
    }

    /**
     * Daftar skripsi yang dibimbing oleh dosen.
     *
     * @return Collection<int, Thesis>
     */
    public function listForSupervisor(int $lecturerId): Collection
    {
        return $this->theses->listForSupervisor($lecturerId);
    }

    /**
     * Detail skripsi untuk dosen pembimbing.
     */
    public function detailForSupervisor(int $lecturerId, int $thesisId): ?Thesis
    {
        $thesis = $this->theses->detail($thesisId);

        if (! $thesis) {
            abort(404, 'Skripsi tidak ditemukan.');
        }

        // Pastikan dosen adalah pembimbing
        if (! in_array($lecturerId, [$thesis->supervisor_1_id, $thesis->supervisor_2_id], true)) {
            abort(403, 'Anda bukan pembimbing skripsi ini.');
        }

        // Muat logs
        $thesis->setRelation('thesisLogs', $this->theses->listLogs($thesisId));

        return $thesis;
    }

    /**
     * Update status skripsi oleh pembimbing.
     */
    public function updateStatus(int $lecturerId, int $thesisId, ThesisStatus $status): void
    {
        $thesis = $this->theses->detail($thesisId);

        if (! $thesis) {
            abort(404, 'Skripsi tidak ditemukan.');
        }

        if (! in_array($lecturerId, [$thesis->supervisor_1_id, $thesis->supervisor_2_id], true)) {
            abort(403, 'Anda bukan pembimbing skripsi ini.');
        }

        $this->theses->updateStatus($thesisId, $status);
    }

    /**
     * Setujui log bimbingan oleh pembimbing.
     */
    public function approveLog(int $lecturerId, int $logId): void
    {
        $thesisLog = ThesisLog::query()
            ->select(['id', 'thesis_id'])
            ->with('thesis:id,supervisor_1_id,supervisor_2_id')
            ->findOrFail($logId);

        $thesis = $thesisLog->thesis;

        if (! $thesis) {
            abort(404, 'Skripsi tidak ditemukan.');
        }

        if (! in_array($lecturerId, [$thesis->supervisor_1_id, $thesis->supervisor_2_id], true)) {
            abort(403, 'Anda bukan pembimbing skripsi ini.');
        }

        $this->theses->approveLog($logId);
    }

    /**
     * Daftar semua skripsi (admin) dengan pagination.
     */
    public function listAll(array $filters): LengthAwarePaginator
    {
        return $this->theses->listAll($filters);
    }

    /**
     * Assign pembimbing skripsi oleh admin.
     */
    public function assignSupervisors(ThesisAssignData $data): void
    {
        $this->theses->assignSupervisors(
            $data->thesis_id,
            $data->supervisor_1_id,
            $data->supervisor_2_id,
        );
    }

    /**
     * Update status skripsi oleh admin.
     */
    public function adminUpdateStatus(int $thesisId, ThesisStatus $status): void
    {
        $this->theses->updateStatus($thesisId, $status);
    }
}
