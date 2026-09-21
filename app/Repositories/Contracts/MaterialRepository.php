<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface MaterialRepository
{
    /**
     * Daftar materi suatu kelas.
     *
     * @return Collection<int, \App\Models\CourseMaterial>
     */
    public function listByOffering(int $offeringId): Collection;

    /**
     * @param  array<string, mixed>  $data
     * @return \App\Models\CourseMaterial
     */
    public function create(array $data): \App\Models\CourseMaterial;

    /**
     * @param  int  $id
     * @param  array<string, mixed>  $data
     * @return \App\Models\CourseMaterial
     */
    public function update(int $id, array $data): \App\Models\CourseMaterial;

    /**
     * @param  int  $id
     */
    public function delete(int $id): void;

    /**
     * @param  int  $id
     * @return \App\Models\CourseMaterial|null
     */
    public function findById(int $id): ?\App\Models\CourseMaterial;
}
