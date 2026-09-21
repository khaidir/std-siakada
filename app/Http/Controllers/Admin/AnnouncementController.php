<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest;
use App\Services\AnnouncementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function __construct(
        private readonly AnnouncementService $announcementService,
    ) {}

    /**
     * Tampilkan halaman daftar pengumuman.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Announcements', $this->announcementService->pageData());
    }

    /**
     * Simpan pengumuman baru.
     */
    public function store(AnnouncementRequest $request): RedirectResponse
    {
        $this->announcementService->create(\App\DTO\AnnouncementData::from($request->validated()));

        return redirect()->back()->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Update pengumuman.
     */
    public function update(AnnouncementRequest $request, int $id): RedirectResponse
    {
        $this->announcementService->update($id, \App\DTO\AnnouncementData::from($request->validated()));

        return redirect()->back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Hapus pengumuman.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $this->announcementService->delete($id);

        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
