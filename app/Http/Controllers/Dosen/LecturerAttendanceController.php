<?php

namespace App\Http\Controllers\Dosen;

use App\DTO\LecturerAttendanceData;
use App\Http\Controllers\Controller;
use App\Http\Requests\LecturerAttendanceRequest;
use App\Models\LecturerAttendance;
use App\Services\LecturerAttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LecturerAttendanceController extends Controller
{
    public function __construct(
        private readonly LecturerAttendanceService $lecturerAttendanceService,
    ) {}

    /**
     * Halaman kehadiran mengajar dosen.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', LecturerAttendance::class);

        $lecturerId = $request->user()->lecturer?->id;

        if (! $lecturerId) {
            abort(403, 'Profil dosen tidak ditemukan.');
        }

        return Inertia::render('Dosen/Kehadiran', $this->lecturerAttendanceService->pageData($lecturerId));
    }

    /**
     * Check-in.
     */
    public function checkIn(LecturerAttendanceRequest $request): RedirectResponse
    {
        $this->authorize('create', LecturerAttendance::class);

        $lecturerId = $request->user()->lecturer?->id;

        if (! $lecturerId) {
            abort(403, 'Profil dosen tidak ditemukan.');
        }

        $data = LecturerAttendanceData::from([
            'course_offering_id' => (int) $request->input('course_offering_id'),
            'date' => $request->input('date'),
            'check_in' => $request->input('check_in', now()->format('H:i:s')),
            'check_out' => null,
        ]);

        $this->lecturerAttendanceService->checkIn($lecturerId, $data);

        return redirect()->back()->with('success', 'Check-in berhasil.');
    }

    /**
     * Check-out.
     */
    public function checkOut(Request $request, int $id): RedirectResponse
    {
        $attendance = LecturerAttendance::query()
            ->select(['id', 'lecturer_id', 'course_offering_id', 'date', 'check_in', 'check_out', 'status'])
            ->findOrFail($id);

        $this->authorize('update', $attendance);

        $lecturerId = $request->user()->lecturer?->id;

        if (! $lecturerId) {
            abort(403, 'Profil dosen tidak ditemukan.');
        }

        $this->lecturerAttendanceService->checkOut($lecturerId, $id);

        return redirect()->back()->with('success', 'Check-out berhasil.');
    }
}
