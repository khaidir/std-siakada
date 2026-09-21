<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KrsMonitoringRequest;
use App\Models\StudyPlan;
use App\Repositories\Contracts\StudyPlanRepository;
use App\Services\KrsMonitoringService;
use Inertia\Inertia;
use Inertia\Response;

class KrsMonitoringController extends Controller
{
    public function __construct(
        private readonly KrsMonitoringService $krsMonitoringService,
        private readonly StudyPlanRepository $studyPlanRepo,
    ) {}

    /**
     * Halaman monitoring KRS.
     */
    public function index(KrsMonitoringRequest $request): Response
    {
        $this->authorize('viewAny', StudyPlan::class);

        return Inertia::render('Admin/KrsMonitoring', $this->krsMonitoringService->pageData(
            $request->validated(),
        ));
    }

    /**
     * Detail KRS mahasiswa.
     */
    public function show(int $id): Response
    {
        $plan = $this->studyPlanRepo->detail($id);

        abort_if(! $plan, 404);

        $this->authorize('view', $plan);

        return Inertia::render('Admin/KrsMonitoringDetail', [
            'plan' => $plan,
        ]);
    }
}
