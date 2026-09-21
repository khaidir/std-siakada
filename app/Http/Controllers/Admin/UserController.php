<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    /**
     * Tampilkan halaman daftar pengguna.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Users', $this->userService->pageData());
    }

    /**
     * Simpan pengguna baru.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $this->userService->create(\App\DTO\UserData::from($request->validated()));

        return redirect()->back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Update pengguna.
     */
    public function update(UserRequest $request, int $id): RedirectResponse
    {
        $this->userService->update($id, \App\DTO\UserData::from($request->validated()));

        return redirect()->back()->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Hapus pengguna.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $this->userService->delete($id);

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
