<?php

namespace App\Repositories\Eloquent;

use App\Models\Announcement;
use App\Repositories\Contracts\AnnouncementRepository as AnnouncementRepositoryContract;
use Illuminate\Support\Collection;

class EloquentAnnouncementRepository implements AnnouncementRepositoryContract
{
    public function listAll(): Collection
    {
        return Announcement::query()
            ->select(['id', 'title', 'content', 'target_role', 'published_at', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): Announcement
    {
        return Announcement::query()->create($data);
    }

    public function update(int $id, array $data): Announcement
    {
        $announcement = Announcement::query()->findOrFail($id);
        $announcement->update($data);

        return $announcement->fresh();
    }

    public function delete(int $id): bool
    {
        return Announcement::query()->where('id', $id)->delete();
    }

    public function findById(int $id): ?Announcement
    {
        return Announcement::query()
            ->select(['id', 'title', 'content', 'target_role', 'published_at'])
            ->where('id', $id)
            ->first();
    }
}
