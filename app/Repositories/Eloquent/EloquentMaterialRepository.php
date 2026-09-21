<?php

namespace App\Repositories\Eloquent;

use App\Models\CourseMaterial;
use App\Repositories\Contracts\MaterialRepository as MaterialRepositoryContract;
use Illuminate\Support\Collection;

class EloquentMaterialRepository implements MaterialRepositoryContract
{
    public function listByOffering(int $offeringId): Collection
    {
        return CourseMaterial::query()
            ->select(['id', 'course_offering_id', 'title', 'description', 'file_path', 'uploaded_by', 'created_at'])
            ->with('uploadedBy:id,name')
            ->where('course_offering_id', $offeringId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function create(array $data): CourseMaterial
    {
        return CourseMaterial::query()->create($data);
    }

    public function update(int $id, array $data): CourseMaterial
    {
        $material = $this->findById($id);

        if (! $material) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                'CourseMaterial not found.',
            );
        }

        $material->update($data);

        return $material->fresh();
    }

    public function delete(int $id): void
    {
        $material = $this->findById($id);

        if (! $material) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                'CourseMaterial not found.',
            );
        }

        // Hapus file jika ada.
        if ($material->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();
    }

    public function findById(int $id): ?CourseMaterial
    {
        return CourseMaterial::query()
            ->select(['id', 'course_offering_id', 'title', 'description', 'file_path', 'uploaded_by', 'created_at'])
            ->where('id', $id)
            ->first();
    }
}
