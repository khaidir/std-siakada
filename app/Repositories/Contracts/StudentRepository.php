<?php

namespace App\Repositories\Contracts;

use App\Models\Student;

interface StudentRepository
{
    public function recalculateGpaAndSks(int $studentId): void;

    public function create(array $data): Student;

    public function findByUserId(int $userId): ?Student;

    /**
     * Data profil gabungan user + student untuk halaman Profile Settings.
     *
     * @return array<string, mixed>|null
     */
    public function profileForUser(int $userId): ?array;

    /**
     * Perbarui kolom biodata milik satu mahasiswa.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateProfile(int $studentId, array $data): void;
}
