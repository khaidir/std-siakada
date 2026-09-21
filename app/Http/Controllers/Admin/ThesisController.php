<?php

namespace App\Http\Controllers\Admin;

use App\DTO\ThesisAssignData;
use App\Enums\ThesisStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignThesisSupervisorRequest;
use App\Http\Requests\UpdateThesisStatusRequest;
use App\Models\Thesis;
use App\Repositories\Contracts\ThesisRepository;
use App\Services\ThesisService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ThesisController extends Controller
{
    public function __construct(
        private readonly ThesisService $thesisService,
        private readonly ThesisRepository $thesisRepo,
    ) {}

    /**
     * Halaman manajemen skripsi.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Thesis::class);

        $filters = request()->only(['study_program_id', 'status']);

        return Inertia::render('Admin/Skripsi', [
            'theses' => $this->thesisService->listAll($filters),
        ]);
    }

    /**
     * Assign pembimbing skripsi.
     */
    public function assign(int $id, AssignThesisSupervisorRequest $request): RedirectResponse
    {
        $thesis = $this->thesisRepo->detail($id);

        abort_if(! $thesis, 404);

        $this->authorize('assignSupervisor', $thesis);

        $data = ThesisAssignData::from([
            'thesis_id' => $id,
            ...$request->validated(),
        ]);

        $this->thesisService->assignSupervisors($data);

        return redirect()->back()->with('success', 'Pembimbing skripsi berhasil ditetapkan.');
    }

    /**
     * Update status skripsi.
     */
    public function updateStatus(int $id, UpdateThesisStatusRequest $request): RedirectResponse
    {
        $thesis = $this->thesisRepo->detail($id);

        abort_if(! $thesis, 404);

        $this->authorize('update', $thesis);

        $status = ThesisStatus::from($request->input('status'));

        $this->thesisService->adminUpdateStatus($id, $status);

        return redirect()->back()->with('success', 'Status skripsi berhasil diperbarui.');
    }
}
