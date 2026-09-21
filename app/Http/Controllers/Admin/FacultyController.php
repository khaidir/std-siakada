<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacultyRequest;
use App\Services\FacultyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FacultyController extends Controller
{
    public function __construct(
        private readonly FacultyService $facultyService,
    ) {}

    /**
     * Tampilkan halaman daftar fakultas.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Faculties', $this->facultyService->pageData());
    }

    /**
     * Simpan fakultas baru.
     */
    public function store(FacultyRequest $request): RedirectResponse
    {
        $this->facultyService->create(\App\DTO\FacultyData::from($request->validated()));

        return redirect()->back()->with('success', 'Fakultas berhasil ditambahkan.');
    }

    /**
     * Update fakultas.
     */
    public function update(FacultyRequest $request, int $id): RedirectResponse
    {
        $this->facultyService->update($id, \App\DTO\FacultyData::from($request->validated()));

        return redirect()->back()->with('success', 'Fakultas berhasil diperbarui.');
    }

    /**
     * Hapus fakultas.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $this->facultyService->delete($id);

        return redirect()->back()->with('success', 'Fakultas berhasil dihapus.');
    }
}
