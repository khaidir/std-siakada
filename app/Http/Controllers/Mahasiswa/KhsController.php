<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\KhsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class KhsController extends Controller
{
    public function __construct(
        protected KhsService $khsService,
    ) {}

    /**
     * Tampilkan halaman KHS.
     */
    public function index(Request $request): Response
    {
        $student = Student::query()
            ->select(['id', 'user_id', 'nim', 'study_program_id', 'entry_year', 'gpa', 'total_sks'])
            ->with('user:id,name')
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $data = $this->khsService->pageData(
            $student,
            $request->integer('semester_id', null) ?: null,
        );

        return Inertia::render('Mahasiswa/Khs', [
            'student' => $student,
            'semesters' => $data['semesters'],
            'selectedSemesterId' => $data['selectedSemesterId'],
            'grades' => $data['grades'],
            'ipSemester' => $data['ipSemester'],
            'ipk' => $data['ipk'],
            'totalSksSemester' => $data['totalSksSemester'],
            'totalSksKumulatif' => $data['totalSksKumulatif'],
        ]);
    }
}
