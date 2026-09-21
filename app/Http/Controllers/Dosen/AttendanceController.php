<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    public function index(Request $request, AttendanceService $service): Response
    {
        $this->authorize('viewAny', Attendance::class);

        $offeringId = $request->query('offering');

        return Inertia::render('Dosen/Presensi', $service->pageData(
            $request->user(),
            $offeringId !== null ? (int) $offeringId : null,
        ));
    }

    public function store(StoreAttendanceRequest $request, AttendanceService $service): RedirectResponse
    {
        $this->authorize('create', Attendance::class);

        $service->store(
            (int) $request->integer('course_offering_id'),
            $request->toBatchAttendanceData(),
        );

        return back()->with('success', 'Presensi berhasil disimpan.');
    }
}
