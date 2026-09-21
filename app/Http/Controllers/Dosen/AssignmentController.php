<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignmentRequest;
use App\Models\Assignment;
use App\Services\AssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssignmentController extends Controller
{
    public function index(Request $request, AssignmentService $service): Response
    {
        $this->authorize('viewAny', Assignment::class);

        $offeringId = $request->query('offering');

        return Inertia::render('Dosen/Tugas', $service->pageData(
            $request->user(),
            $offeringId !== null ? (int) $offeringId : null,
        ));
    }

    public function store(AssignmentRequest $request, AssignmentService $service): RedirectResponse
    {
        $this->authorize('create', Assignment::class);

        $service->store(
            $request->user(),
            $request->toAssignmentData(),
        );

        return back()->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function update(AssignmentRequest $request, AssignmentService $service, int $id): RedirectResponse
    {
        $assignment = Assignment::query()->findOrFail($id);

        $this->authorize('update', $assignment);

        $service->update(
            $request->user(),
            $id,
            $request->toAssignmentData(),
        );

        return back()->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Request $request, AssignmentService $service, int $id): RedirectResponse
    {
        $assignment = Assignment::query()->findOrFail($id);

        $this->authorize('delete', $assignment);

        $service->destroy($request->user(), $id);

        return back()->with('success', 'Tugas berhasil dihapus.');
    }
}
