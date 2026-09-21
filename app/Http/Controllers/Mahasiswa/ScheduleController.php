<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\PresenceService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    public function __construct(
        protected PresenceService $presenceService,
    ) {}

    /**
     * Tampilkan halaman jadwal kuliah.
     */
    public function index(): Response
    {
        $studentId = (int) Auth::user()->student?->id;

        $data = $this->presenceService->scheduleData($studentId);

        return Inertia::render('Mahasiswa/Jadwal', [
            'schedules' => $data['schedules'],
            'semesterLabel' => $data['semesterLabel'],
        ]);
    }
}
