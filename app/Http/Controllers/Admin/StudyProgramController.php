<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudyProgramRequest;
use App\Services\StudyProgramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudyProgramController extends Controller
{
    public function __construct(
        private readonly StudyProgramService $studyProgramService,
    ) {}

    /**
     * Tampilkan halaman daftar program studi.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/StudyPrograms', $this->studyProgramService->pageData());
    }

    /**
     * Simpan program studi baru.
     */
    public function store(StudyProgramRequest $request): RedirectResponse
    {
        $this->studyProgramService->create(\App\DTO\StudyProgramData::from($request->validated()));

        return redirect()->back()->with('success', 'Program studi berhasil ditambahkan.');
    }

    /**
     * Update program studi.
     */
    public function update(StudyProgramRequest $request, int $id): RedirectResponse
    {
        $this->studyProgramService->update($id, \App\DTO\StudyProgramData::from($request->validated()));

        return redirect()->back()->with('success', 'Program studi berhasil diperbarui.');
    }

    /**
     * Hapus program studi.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $this->studyProgramService->delete($id);

        return redirect()->back()->with('success', 'Program studi berhasil dihapus.');
    }
}
