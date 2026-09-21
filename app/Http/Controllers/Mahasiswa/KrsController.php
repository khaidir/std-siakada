<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKrsRequest;
use App\Services\KrsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KrsController extends Controller
{
    public function __construct(
        private readonly KrsService $krsService,
    ) {}

    /**
     * Tampilkan halaman KRS.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Mahasiswa/Krs', $this->krsService->pageData($user));
    }

    /**
     * Tambah mata kuliah ke KRS.
     */
    public function store(StoreKrsRequest $request): RedirectResponse
    {
        $user = $request->user();

        $this->krsService->addCourse($user, (int) $request->input('course_offering_id'));

        return redirect()->back()->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    /**
     * Hapus mata kuliah dari KRS.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();

        $this->krsService->removeCourse($user, $id);

        return redirect()->back()->with('success', 'Mata kuliah berhasil dihapus.');
    }

    /**
     * Submit KRS.
     */
    public function submit(Request $request): RedirectResponse
    {
        $user = $request->user();

        $this->krsService->submit($user);

        return redirect()->back()->with('success', 'KRS berhasil disubmit.');
    }
}
