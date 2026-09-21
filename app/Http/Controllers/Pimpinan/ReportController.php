<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\StudyProgram;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {}

    /**
     * Tampilkan halaman laporan akademik.
     */
    public function index(Request $request): Response
    {
        $this->authorize('view', \App\Models\Report::class);

        $semesterId = $request->integer('semester_id', 0) ?: null;
        $studyProgramId = $request->integer('study_program_id', 0) ?: null;

        $activeTab = $request->input('tab', 'khs');

        $data = [
            'active_tab' => $activeTab,
            'filters' => [
                'semester_id' => $semesterId,
                'study_program_id' => $studyProgramId,
            ],
            'semesters' => Semester::query()
                ->select(['semesters.id', 'academic_years.name as academic_year', 'semesters.type'])
                ->join('academic_years', 'semesters.academic_year_id', '=', 'academic_years.id')
                ->orderBy('semesters.start_date', 'desc')
                ->get()
                ->map(fn (Semester $s) => [
                    'id' => $s->id,
                    'label' => $s->academic_year . ' - ' . ($s->type?->value ?? $s->type),
                ]),
            'study_programs' => StudyProgram::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get()
                ->map(fn (StudyProgram $p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                ]),
        ];

        if ($activeTab === 'khs' || $activeTab === 'transkrip') {
            $data['grade_reports'] = $this->reportService->studentGradeReports($semesterId, $studyProgramId);
        }

        if ($activeTab === 'presensi') {
            $data['attendance_reports'] = $this->reportService->studentAttendance($semesterId, $studyProgramId);
        }

        if ($activeTab === 'kinerja_dosen') {
            $data['lecturer_performance'] = $this->reportService->lecturerPerformance($semesterId);
        }

        return Inertia::render('pimpinan/Laporan', $data);
    }
}
