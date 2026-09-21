<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Render halaman dashboard sesuai peran pengguna yang terautentikasi.
     */
    public function __invoke(Request $request, DashboardService $dashboards): Response
    {
        $user = $request->user();
        $role = $user->getRoleNames()->first();

        return Inertia::render($this->pageFor($role), [
            'stats' => $dashboards->for($user),
        ]);
    }

    private function pageFor(string $role): string
    {
        return match ($role) {
            'super-admin' => 'Admin/Dashboard',
            'kaprodi' => 'Kaprodi/Dashboard',
            'dosen' => 'Dosen/Dashboard',
            'mahasiswa' => 'Mahasiswa/Dashboard',
            'pimpinan' => 'Pimpinan/Dashboard',
            default => 'Dashboard',
        };
    }
}
