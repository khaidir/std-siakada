<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\DashboardRepository;

class DashboardService
{
    public function __construct(
        private readonly DashboardRepository $dashboards,
    ) {}

    /**
     * Kumpulkan statistik dashboard sesuai peran pengguna.
     *
     * @return array<string, mixed>
     */
    public function for(User $user): array
    {
        $role = $user->getRoleNames()->first();

        return match ($role) {
            'super-admin' => $this->dashboards->superAdminStats(),
            'kaprodi' => $this->dashboards->kaprodiStats((int) $user->lecturer?->study_program_id),
            'dosen' => $this->dashboards->dosenStats((int) $user->lecturer?->id),
            'mahasiswa' => $this->dashboards->mahasiswaStats((int) $user->student?->id),
            'pimpinan' => $this->dashboards->pimpinanStats(),
            default => [],
        };
    }
}
