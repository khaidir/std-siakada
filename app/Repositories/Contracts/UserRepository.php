<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface UserRepository
{
    /**
     * Daftar pengguna dengan role.
     *
     * @return Collection<int, \App\Models\User>
     */
    public function listWithRole(): Collection;

    /**
     * Cari user berdasarkan email.
     */
    public function findByEmail(string $email): ?\App\Models\User;

    /**
     * Cari user berdasarkan id.
     */
    public function findById(int $id): ?\App\Models\User;

    public function create(array $data): \App\Models\User;

    public function update(int $id, array $data): \App\Models\User;

    public function delete(int $id): bool;
}
