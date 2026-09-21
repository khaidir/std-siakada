<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmissionGradeRequest;
use App\Models\Assignment;
use App\Services\AssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubmissionGradeController extends Controller
{
    public function index(Request $request, AssignmentService $service, int $assignment): Response
    {
        $assignmentModel = Assignment::query()->findOrFail($assignment);

        $this->authorize('view', $assignmentModel);

        return Inertia::render('Dosen/PenilaianTugas', $service->submissionData(
            $request->user(),
            $assignment,
        ));
    }

    public function store(SubmissionGradeRequest $request, AssignmentService $service, int $assignment): RedirectResponse
    {
        $assignmentModel = Assignment::query()->findOrFail($assignment);

        $this->authorize('update', $assignmentModel);

        $service->gradeSubmissions(
            $request->user(),
            $assignment,
            $request->input('grades', []),
        );

        return back()->with('success', 'Nilai submission berhasil disimpan.');
    }
}
