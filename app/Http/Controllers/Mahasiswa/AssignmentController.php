<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitRequest;
use App\Services\SubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssignmentController extends Controller
{
    public function __construct(
        private readonly SubmissionService $submissionService,
    ) {}

    /**
     * Tampilkan halaman daftar tugas.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $studentId = (int) $user->student?->id;

        return Inertia::render('Mahasiswa/Tugas', $this->submissionService->pageData($studentId));
    }

    /**
     * Submit tugas.
     */
    public function submit(SubmitRequest $request): RedirectResponse
    {
        $user = $request->user();
        $studentId = (int) $user->student?->id;

        $this->submissionService->submit(
            $studentId,
            (int) $request->input('assignment_id'),
            $request->file('file'),
        );

        return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan.');
    }
}
