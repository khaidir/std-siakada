<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicYearRequest;
use App\Http\Requests\SemesterRequest;
use App\Services\AcademicPeriodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AcademicPeriodController extends Controller
{
    public function __construct(
        private readonly AcademicPeriodService $periodService,
    ) {}

    /**
     * Tampilkan halaman periode akademik.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/AcademicPeriods', $this->periodService->pageData());
    }

    /**
     * Simpan tahun ajaran baru.
     */
    public function storeAcademicYear(AcademicYearRequest $request): RedirectResponse
    {
        $this->periodService->createAcademicYear(\App\DTO\AcademicYearData::from($request->validated()));

        return redirect()->back()->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Update tahun ajaran.
     */
    public function updateAcademicYear(AcademicYearRequest $request, int $id): RedirectResponse
    {
        $this->periodService->updateAcademicYear($id, \App\DTO\AcademicYearData::from($request->validated()));

        return redirect()->back()->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /**
     * Hapus tahun ajaran.
     */
    public function destroyAcademicYear(Request $request, int $id): RedirectResponse
    {
        $this->periodService->deleteAcademicYear($id);

        return redirect()->back()->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    /**
     * Simpan semester baru.
     */
    public function storeSemester(SemesterRequest $request): RedirectResponse
    {
        $this->periodService->createSemester(\App\DTO\SemesterData::from($request->validated()));

        return redirect()->back()->with('success', 'Semester berhasil ditambahkan.');
    }

    /**
     * Update semester.
     */
    public function updateSemester(SemesterRequest $request, int $id): RedirectResponse
    {
        $this->periodService->updateSemester($id, \App\DTO\SemesterData::from($request->validated()));

        return redirect()->back()->with('success', 'Semester berhasil diperbarui.');
    }

    /**
     * Hapus semester.
     */
    public function destroySemester(Request $request, int $id): RedirectResponse
    {
        $this->periodService->deleteSemester($id);

        return redirect()->back()->with('success', 'Semester berhasil dihapus.');
    }
}
