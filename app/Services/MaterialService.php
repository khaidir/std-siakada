<?php

namespace App\Services;

use App\DTO\MaterialData;
use App\Models\CourseOffering;
use App\Models\User;
use App\Repositories\Contracts\CourseOfferingRepository;
use App\Repositories\Contracts\MaterialRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MaterialService
{
    public function __construct(
        private readonly MaterialRepository $materials,
        private readonly CourseOfferingRepository $offerings,
    ) {}

    /**
     * Data halaman materi: daftar kelas diampu + materi kelas terpilih.
     *
     * @return array<string, mixed>
     */
    public function pageData(User $user, ?int $offeringId = null): array
    {
        $lecturerId = (int) $user->lecturer?->id;

        $offerings = $this->offerings->listByLecturer($lecturerId);

        $offeringOptions = $offerings
            ->map(fn (CourseOffering $o) => [
                'id' => $o->id,
                'label' => trim(sprintf(
                    '%s (%s) · %s · %s',
                    $o->course?->name,
                    $o->course?->code,
                    $o->semester?->type?->value ?? '',
                    $o->day?->value ?? '',
                )),
            ])
            ->values()
            ->all();

        $selected = $offeringId ?? (int) ($offerings->first()?->id ?? 0);

        return [
            'offerings' => $offeringOptions,
            'materials' => $selected > 0 ? $this->materialsData($selected) : [],
            'selected_offering_id' => $selected,
        ];
    }

    /**
     * Simpan materi baru.
     */
    public function store(MaterialData $data, ?UploadedFile $file = null): void
    {
        $row = [
            'course_offering_id' => $data->course_offering_id,
            'title' => $data->title,
            'description' => $data->description,
            'uploaded_by' => $data->uploaded_by,
        ];

        if ($file) {
            $row['file_path'] = $file->store('materials', 'public');
        }

        $this->materials->create($row);
    }

    /**
     * Update materi yang sudah ada.
     */
    public function update(int $id, MaterialData $data, ?UploadedFile $file = null): void
    {
        $material = $this->materials->findById($id);

        if (! $material) {
            throw ValidationException::withMessages([
                'material' => 'Materi tidak ditemukan.',
            ]);
        }

        $row = [
            'title' => $data->title,
            'description' => $data->description,
        ];

        if ($file) {
            // Hapus file lama jika ada.
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }

            $row['file_path'] = $file->store('materials', 'public');
        }

        $this->materials->update($id, $row);
    }

    /**
     * Hapus materi.
     */
    public function destroy(int $id): void
    {
        $this->materials->delete($id);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function materialsData(int $offeringId): array
    {
        return $this->materials->listByOffering($offeringId)
            ->map(fn (\App\Models\CourseMaterial $m) => [
                'id' => $m->id,
                'title' => $m->title,
                'description' => $m->description,
                'file_path' => $m->file_path,
                'file_url' => $m->file_path ? Storage::disk('public')->url($m->file_path) : null,
                'uploaded_by' => $m->uploadedBy?->name,
                'created_at' => $m->created_at?->toDateString(),
            ])
            ->all();
    }
}
