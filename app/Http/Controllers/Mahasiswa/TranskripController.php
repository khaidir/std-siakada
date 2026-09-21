<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\TranskripService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TranskripController extends Controller
{
    public function __construct(
        protected TranskripService $transkripService,
    ) {}

    /**
     * Tampilkan halaman transkrip.
     */
    public function index(): Response
    {
        $student = Student::query()
            ->select(['id', 'user_id', 'nim', 'study_program_id', 'entry_year', 'gpa', 'total_sks'])
            ->with('user:id,name')
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $data = $this->transkripService->pageData($student);

        return Inertia::render('Mahasiswa/Transkrip', [
            'student' => $data['student'],
            'semesterGroups' => $data['semesterGroups'],
            'ipk' => $data['ipk'],
            'totalSks' => $data['totalSks'],
        ]);
    }
}
