<?php

namespace App\Services;

use App\DTO\KrsApprovalData;
use App\Enums\StudyPlanDetailStatus;
use App\Enums\StudyPlanStatus;
use App\Repositories\Contracts\StudyPlanRepository;
use Illuminate\Support\Facades\DB;

class KrsApprovalService
{
    public function __construct(
        private readonly StudyPlanRepository $studyPlanRepo,
    ) {}

    /**
     * Daftar KRS submitted untuk bimbingan PA.
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\StudyPlan>
     */
    public function listForAdvisor(int $lecturerId)
    {
        return $this->studyPlanRepo->listForAdvisor($lecturerId);
    }

    /**
     * Detail KRS.
     */
    public function detail(int $planId)
    {
        return $this->studyPlanRepo->detail($planId);
    }

    /**
     * Setujui KRS.
     */
    public function approve(int $lecturerId, KrsApprovalData $data): void
    {
        $plan = $this->studyPlanRepo->detail($data->study_plan_id);

        if (! $plan || $plan->status !== StudyPlanStatus::Submitted->value) {
            abort(404, 'KRS tidak ditemukan atau sudah diproses.');
        }

        // Pastikan dosen PA = prodi mahasiswa
        if ($plan->student->study_program_id !== $this->getLecturerStudyProgram($lecturerId)) {
            abort(403, 'Anda tidak berwenang menyetujui KRS ini.');
        }

        DB::transaction(function () use ($plan, $lecturerId) {
            $plan->status = StudyPlanStatus::Approved->value;
            $plan->approved_by = $lecturerId;
            $plan->approved_at = now();
            $plan->save();

            // Set semua detail menjadi approved
            $plan->studyPlanDetails()->update([
                'status' => StudyPlanDetailStatus::Approved->value,
            ]);
        });
    }

    /**
     * Tolak KRS.
     */
    public function reject(int $lecturerId, KrsApprovalData $data): void
    {
        $plan = $this->studyPlanRepo->detail($data->study_plan_id);

        if (! $plan || $plan->status !== StudyPlanStatus::Submitted->value) {
            abort(404, 'KRS tidak ditemukan atau sudah diproses.');
        }

        if ($plan->student->study_program_id !== $this->getLecturerStudyProgram($lecturerId)) {
            abort(403, 'Anda tidak berwenang menolak KRS ini.');
        }

        DB::transaction(function () use ($plan, $data) {
            $plan->status = StudyPlanStatus::Rejected->value;
            $plan->notes = $data->reason;
            $plan->save();

            // Kembalikan detail ke pending
            $plan->studyPlanDetails()->update([
                'status' => StudyPlanDetailStatus::Pending->value,
            ]);
        });
    }

    private function getLecturerStudyProgram(int $lecturerId): ?int
    {
        return \App\Models\Lecturer::query()
            ->select('study_program_id')
            ->where('user_id', $lecturerId)
            ->value('study_program_id');
    }
}
