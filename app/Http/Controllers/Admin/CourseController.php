<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Services\Admin\AdminCourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function __construct(
        private readonly AdminCourseService $courseService,
    ) {}

    /**
     * Tampilkan halaman daftar mata kuliah (semua prodi).
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Courses', $this->courseService->pageData());
    }

    /**
     * Simpan mata kuliah baru.
     */
    public function store(CourseRequest $request): RedirectResponse
    {
        $this->courseService->create(\App\DTO\CourseData::from($request->validated()));

        return redirect()->back()->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    /**
     * Update mata kuliah.
     */
    public function update(CourseRequest $request, int $id): RedirectResponse
    {
        $this->courseService->update($id, \App\DTO\CourseData::from($request->validated()));

        return redirect()->back()->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * Hapus mata kuliah.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $this->courseService->delete($id);

        return redirect()->back()->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
