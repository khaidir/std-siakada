<?php

namespace App\Http\Controllers\Dosen;

use App\DTO\KrsApprovalData;
use App\Http\Controllers\Controller;
use App\Http\Requests\KrsApprovalRequest;
use App\Services\KrsApprovalService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KrsApprovalController extends Controller
{
    public function __construct(
        private readonly KrsApprovalService $krsApprovalService,
    ) {}

    /**
     * Daftar KRS submitted untuk bimbingan PA.
     */
    public function index(): Response
    {
        $lecturerId = auth()->id();
        $plans = $this->krsApprovalService->listForAdvisor($lecturerId);

        return Inertia::render('Dosen/BimbinganPa', [
            'plans' => $plans,
        ]);
    }

    /**
     * Detail KRS.
     */
    public function show(int $id): Response
    {
        $plan = $this->krsApprovalService->detail($id);

        if (! $plan) {
            abort(404, 'KRS tidak ditemukan.');
        }

        return Inertia::render('Dosen/BimbinganPaDetail', [
            'plan' => $plan,
        ]);
    }

    /**
     * Setujui KRS.
     */
    public function approve(int $id, KrsApprovalRequest $request): RedirectResponse
    {
        $lecturerId = auth()->id();
        $data = KrsApprovalData::from([
            'study_plan_id' => $id,
            'decision' => 'approved',
            'reason' => null,
        ]);

        $this->krsApprovalService->approve($lecturerId, $data);

        return redirect()->route('dosen.bimbingan-pa.index')
            ->with('success', 'KRS berhasil disetujui.');
    }

    /**
     * Tolak KRS.
     */
    public function reject(int $id, KrsApprovalRequest $request): RedirectResponse
    {
        $lecturerId = auth()->id();
        $data = KrsApprovalData::from([
            'study_plan_id' => $id,
            'decision' => 'rejected',
            'reason' => $request->input('reason'),
        ]);

        $this->krsApprovalService->reject($lecturerId, $data);

        return redirect()->route('dosen.bimbingan-pa.index')
            ->with('success', 'KRS berhasil ditolak.');
    }
}
