<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\GradeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GradeController extends Controller
{
    public function __construct(
        private readonly GradeService $gradeService,
    ) {}

    /**
     * Tampilkan halaman nilai mahasiswa.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $studentId = (int) $user->student?->id;

        return Inertia::render('Mahasiswa/Nilai', $this->gradeService->summaryForStudent($studentId));
    }
}
