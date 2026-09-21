<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Services\CourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function __construct(
        private readonly CourseService $courseService,
    ) {}

    /**
     * Tampilkan halaman daftar mata kuliah.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Kaprodi/Courses', $this->courseService->pageData($user));
    }

    /**
     * Simpan mata kuliah baru.
     */
    public function store(CourseRequest $request): RedirectResponse
    {
        $user = $request->user();

        $this->courseService->create($user, \App\DTO\CourseData::from($request->validated()));

        return redirect()->back()->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    /**
     * Update mata kuliah.
     */
    public function update(CourseRequest $request, int $id): RedirectResponse
    {
        $user = $request->user();

        $this->courseService->update($user, $id, \App\DTO\CourseData::from($request->validated()));

        return redirect()->back()->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * Hapus mata kuliah.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();

        $this->courseService->delete($user, $id);

        return redirect()->back()->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
