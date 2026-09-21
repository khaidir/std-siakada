<?php

namespace App\Http\Controllers\Dosen;

use App\Enums\ThesisStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveThesisLogRequest;
use App\Http\Requests\UpdateThesisStatusRequest;
use App\Services\ThesisService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ThesisController extends Controller
{
    public function __construct(
        private readonly ThesisService $thesisService,
    ) {}

    /**
     * Daftar skripsi bimbingan.
     */
    public function index(): Response
    {
        $lecturerId = auth()->user()->lecturer?->id;

        if (! $lecturerId) {
            abort(403, 'Profil dosen tidak ditemukan.');
        }

        $theses = $this->thesisService->listForSupervisor($lecturerId);

        return Inertia::render('Dosen/BimbinganSkripsi', [
            'theses' => $theses,
        ]);
    }

    /**
     * Detail skripsi.
     */
    public function show(int $id): Response
    {
        $lecturerId = auth()->user()->lecturer?->id;

        if (! $lecturerId) {
            abort(403, 'Profil dosen tidak ditemukan.');
        }

        $thesis = $this->thesisService->detailForSupervisor($lecturerId, $id);

        return Inertia::render('Dosen/BimbinganSkripsiDetail', [
            'thesis' => $thesis,
        ]);
    }

    /**
     * Update status skripsi.
     */
    public function updateStatus(int $id, UpdateThesisStatusRequest $request): RedirectResponse
    {
        $lecturerId = auth()->user()->lecturer?->id;

        if (! $lecturerId) {
            abort(403, 'Profil dosen tidak ditemukan.');
        }

        $status = ThesisStatus::from($request->input('status'));

        $this->thesisService->updateStatus($lecturerId, $id, $status);

        return redirect()->back()->with('success', 'Status skripsi berhasil diperbarui.');
    }

    /**
     * Setujui log bimbingan.
     */
    public function approveLog(int $id, ApproveThesisLogRequest $request): RedirectResponse
    {
        $lecturerId = auth()->user()->lecturer?->id;

        if (! $lecturerId) {
            abort(403, 'Profil dosen tidak ditemukan.');
        }

        $logId = (int) $request->input('log_id');

        $this->thesisService->approveLog($lecturerId, $logId);

        return redirect()->back()->with('success', 'Log bimbingan berhasil disetujui.');
    }
}
