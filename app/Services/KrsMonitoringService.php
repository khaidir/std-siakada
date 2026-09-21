<?php

namespace App\Services;

use App\Repositories\Contracts\StudyPlanRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class KrsMonitoringService
{
    public function __construct(
        private readonly StudyPlanRepository $studyPlanRepo,
    ) {}

    /**
     * Data halaman monitoring KRS.
     *
     * @return array{plans: LengthAwarePaginator, summary: array}
     */
    public function pageData(array $filters = []): array
    {
        $plans = $this->studyPlanRepo->listAll($filters);

        return [
            'plans' => $plans,
            'summary' => $this->summary(),
        ];
    }

    /**
     * Statistik ringkas jumlah KRS per status.
     */
    public function summary(): array
    {
        return [
            'total' => \App\Models\StudyPlan::count(),
            'draft' => \App\Models\StudyPlan::where('status', 'draft')->count(),
            'submitted' => \App\Models\StudyPlan::where('status', 'submitted')->count(),
            'approved' => \App\Models\StudyPlan::where('status', 'approved')->count(),
            'rejected' => \App\Models\StudyPlan::where('status', 'rejected')->count(),
        ];
    }
}
