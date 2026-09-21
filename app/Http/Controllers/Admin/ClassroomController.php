<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassroomRequest;
use App\Services\ClassroomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClassroomController extends Controller
{
    public function __construct(
        private readonly ClassroomService $classroomService,
    ) {}

    /**
     * Tampilkan halaman daftar ruangan.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Classrooms', $this->classroomService->pageData());
    }

    /**
     * Simpan ruangan baru.
     */
    public function store(ClassroomRequest $request): RedirectResponse
    {
        $this->classroomService->create(\App\DTO\ClassroomData::from($request->validated()));

        return redirect()->back()->with('success', 'Ruangan berhasil ditambahkan.');
    }

    /**
     * Update ruangan.
     */
    public function update(ClassroomRequest $request, int $id): RedirectResponse
    {
        $this->classroomService->update($id, \App\DTO\ClassroomData::from($request->validated()));

        return redirect()->back()->with('success', 'Ruangan berhasil diperbarui.');
    }

    /**
     * Hapus ruangan.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $this->classroomService->delete($id);

        return redirect()->back()->with('success', 'Ruangan berhasil dihapus.');
    }
}
