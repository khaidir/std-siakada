<?php

namespace App\Repositories\Contracts;

use App\Models\Announcement;
use Illuminate\Support\Collection;

interface AnnouncementRepository
{
    /**
     * @return Collection<int, \App\Models\Announcement>
     */
    public function listAll(): Collection;

    public function create(array $data): Announcement;

    public function update(int $id, array $data): Announcement;

    public function delete(int $id): bool;

    public function findById(int $id): ?Announcement;
}
