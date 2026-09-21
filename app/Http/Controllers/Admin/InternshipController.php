<?php

namespace App\Http\Controllers\Admin;

use App\DTO\InternshipAssignData;
use App\Enums\InternshipStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignInternshipSupervisorRequest;
use App\Http\Requests\UpdateInternshipStatusRequest;
use App\Models\Internship;
use App\Repositories\Contracts\InternshipRepository;
use App\Services\InternshipService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InternshipController extends Controller
{
    public function __construct(
        private readonly InternshipService $internshipService,
        private readonly InternshipRepository $internshipRepo,
    ) {}

    /**
     * Halaman manajemen KP.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Internship::class);

        $filters = request()->only(['study_program_id', 'status']);

        return Inertia::render('Admin/Kp', [
            'internships' => $this->internshipService->listAll($filters),
        ]);
    }

    /**
     * Assign dosen pembimbing KP.
     */
    public function assign(int $id, AssignInternshipSupervisorRequest $request): RedirectResponse
    {
        $internship = $this->internshipRepo->detail($id);

        abort_if(! $internship, 404);

        $this->authorize('assignSupervisor', $internship);

        $data = InternshipAssignData::from([
            'internship_id' => $id,
            ...$request->validated(),
        ]);

        $this->internshipService->assignSupervisor($data);

        return redirect()->back()->with('success', 'Pembimbing KP berhasil ditetapkan.');
    }

    /**
     * Update status KP.
     */
    public function updateStatus(int $id, UpdateInternshipStatusRequest $request): RedirectResponse
    {
        $internship = Internship::query()->select(['id', 'student_id', 'supervisor_id'])->find($id);

        abort_if(! $internship, 404);

        $this->authorize('update', $internship);

        $status = InternshipStatus::from($request->input('status'));

        $this->internshipService->adminUpdateStatus($id, $status);

        return redirect()->back()->with('success', 'Status KP berhasil diperbarui.');
    }
}
