<?php

namespace App\Services;

use App\DTO\AnnouncementData;
use App\Models\Announcement;
use App\Repositories\Contracts\AnnouncementRepository;

class AnnouncementService
{
    public function __construct(
        private readonly AnnouncementRepository $announcements,
    ) {}

    /**
     * Data halaman daftar pengumuman.
     *
     * @return array<string, mixed>
     */
    public function pageData(): array
    {
        $items = $this->announcements->listAll()
            ->map(fn (Announcement $a) => [
                'id' => $a->id,
                'title' => $a->title,
                'content' => $a->content,
                'target_role' => $a->target_role,
                'published_at' => $a->published_at?->format('d M Y H:i'),
                'created_at' => $a->created_at?->format('d M Y'),
            ])
            ->values()
            ->all();

        return ['announcements' => $items];
    }

    public function create(AnnouncementData $data): void
    {
        $this->announcements->create([
            'title' => $data->title,
            'content' => $data->content,
            'target_role' => $data->target_role,
            'published_at' => $data->published_at,
        ]);
    }

    public function update(int $id, AnnouncementData $data): void
    {
        $this->announcements->update($id, [
            'title' => $data->title,
            'content' => $data->content,
            'target_role' => $data->target_role,
            'published_at' => $data->published_at,
        ]);
    }

    public function delete(int $id): void
    {
        $this->announcements->delete($id);
    }
}
