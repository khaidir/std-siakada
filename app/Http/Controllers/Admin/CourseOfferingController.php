<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseOfferingRequest;
use App\Services\CourseOfferingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseOfferingController extends Controller
{
    public function __construct(
        private readonly CourseOfferingService $offeringService,
    ) {}

    /**
     * Tampilkan halaman daftar kelas & jadwal.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/CourseOfferings', $this->offeringService->pageData());
    }

    /**
     * Simpan kelas/jadwal baru.
     */
    public function store(CourseOfferingRequest $request): RedirectResponse
    {
        $this->offeringService->create(\App\DTO\CourseOfferingData::from($request->validated()));

        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Update kelas/jadwal.
     */
    public function update(CourseOfferingRequest $request, int $id): RedirectResponse
    {
        $this->offeringService->update($id, \App\DTO\CourseOfferingData::from($request->validated()));

        return redirect()->back()->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Hapus kelas/jadwal.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $this->offeringService->delete($id);

        return redirect()->back()->with('success', 'Kelas berhasil dihapus.');
    }
}
