<?php

namespace App\Http\Controllers\Mahasiswa;

use App\DTO\InternshipLogData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInternshipLogRequest;
use App\Services\InternshipService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class InternshipController extends Controller
{
    public function __construct(
        protected InternshipService $internshipService,
    ) {}

    /**
     * Tampilkan halaman KP.
     */
    public function index(): Response
    {
        $studentId = (int) Auth::user()->student?->id;

        $data = $this->internshipService->dataForStudent($studentId);

        return Inertia::render('Mahasiswa/Kp', [
            'internship' => $data['internship'],
            'logs' => $data['logs'],
        ]);
    }

    /**
     * Tambah logbook harian.
     */
    public function storeLog(StoreInternshipLogRequest $request)
    {
        $studentId = (int) Auth::user()->student?->id;

        $data = InternshipLogData::from($request->validated());

        $this->internshipService->addLog($studentId, $data);

        return redirect()->back()->with('success', 'Logbook berhasil ditambahkan.');
    }
}
