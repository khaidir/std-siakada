<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\LecturerAttendance;
use App\Models\Semester;
use App\Services\LecturerAttendanceService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LecturerAttendanceController extends Controller
{
    public function index(Request $request, LecturerAttendanceService $service): Response
    {
        $this->authorize('viewAny', LecturerAttendance::class);

        $user = $request->user();
        $studyProgramId = $user?->lecturer?->study_program_id;

        $filters = [
            'study_program_id' => $studyProgramId,
            'semester_id' => $request->integer('semester_id') ?: null,
            'lecturer_id' => $request->integer('lecturer_id') ?: null,
        ];

        $semesters = Semester::query()
            ->select(['id', 'type', 'academic_year_id'])
            ->with('academicYear:id,code,name')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'label' => $s->academicYear?->name . ' - ' . ($s->type?->value ?? ''),
            ]);

        $result = $service->summary($filters);

        return Inertia::render('Kaprodi/KehadiranDosen', [
            'summary' => $result['summary'],
            'stats' => $result['stats'],
            'semesters' => $semesters,
            'filters' => $filters,
        ]);
    }

    public function show(Request $request, LecturerAttendanceService $service, int $lecturerId): Response
    {
        $attendance = \App\Models\LecturerAttendance::query()
            ->select(['id', 'lecturer_id'])
            ->where('lecturer_id', $lecturerId)
            ->firstOrFail();

        $this->authorize('view', $attendance);

        $semesterId = $request->integer('semester_id') ?: null;

        if (! $semesterId) {
            $activeSemester = Semester::query()
                ->select(['id'])
                ->where('is_active', true)
                ->first();

            $semesterId = $activeSemester?->id ?? 0;
        }

        $detail = $service->detail($lecturerId, $semesterId);

        return Inertia::render('Kaprodi/KehadiranDosenDetail', [
            'lecturer_id' => $lecturerId,
            'semester_id' => $semesterId,
            'detail' => $detail,
        ]);
    }
}
