<?php

namespace App\Http\Controllers\Mahasiswa;

use App\DTO\ThesisLogData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreThesisLogRequest;
use App\Services\ThesisService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ThesisController extends Controller
{
    public function __construct(
        protected ThesisService $thesisService,
    ) {}

    /**
     * Tampilkan halaman skripsi.
     */
    public function index(): Response
    {
        $studentId = (int) Auth::user()->student?->id;

        $data = $this->thesisService->dataForStudent($studentId);

        return Inertia::render('Mahasiswa/Skripsi', [
            'thesis' => $data['thesis'],
            'logs' => $data['logs'],
        ]);
    }

    /**
     * Tambah log bimbingan.
     */
    public function storeLog(StoreThesisLogRequest $request)
    {
        $studentId = (int) Auth::user()->student?->id;

        $data = ThesisLogData::from($request->validated());

        $this->thesisService->addLog($studentId, $data);

        return redirect()->back()->with('success', 'Log bimbingan berhasil ditambahkan.');
    }
}
