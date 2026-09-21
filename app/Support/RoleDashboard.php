<?php

namespace App\Support;

final class RoleDashboard
{
    /**
     * Pemetaan role ke path dashboard setelah login.
     *
     * @var array<string, string>
     */
    private const PATHS = [
        'super-admin' => '/admin/dashboard',
        'kaprodi' => '/kaprodi/dashboard',
        'dosen' => '/dosen/dashboard',
        'mahasiswa' => '/mahasiswa/dashboard',
        'pimpinan' => '/pimpinan/dashboard',
    ];

    public static function pathFor(string $role): string
    {
        return self::PATHS[$role] ?? '/dashboard';
    }
}
