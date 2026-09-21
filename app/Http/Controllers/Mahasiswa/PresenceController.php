<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\PresenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PresenceController extends Controller
{
    public function __construct(
        protected PresenceService $presenceService,
    ) {}

    /**
     * Tampilkan halaman presensi.
     */
    public function index(Request $request): Response
    {
        $studentId = (int) Auth::user()->student?->id;

        $data = $this->presenceService->presenceData(
            $studentId,
            $request->integer('offering_id', null) ?: null,
        );

        return Inertia::render('Mahasiswa/Presensi', [
            'offerings' => $data['offerings'],
            'selectedOfferingId' => $data['selectedOfferingId'],
            'attendances' => $data['attendances'],
            'percentage' => $data['percentage'],
        ]);
    }
}
