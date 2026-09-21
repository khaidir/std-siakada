<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface StudyPlanDetailRepository
{
    /**
     * Daftar mahasiswa (detail KRS) berstatus approved pada suatu kelas.
     *
     * @return Collection<int, \App\Models\StudyPlanDetail>
     */
    public function listApprovedByOffering(int $offeringId): Collection;

    /**
     * Ambil detail KRS approved yang cocok dengan offering & daftar id.
     *
     * @param  array<int, int>  $detailIds
     * @return Collection<int, \App\Models\StudyPlanDetail>
     */
    public function approvedForOffering(int $offeringId, array $detailIds): Collection;

    /**
     * Daftar detail KRS milik suatu study plan.
     *
     * @return Collection<int, \App\Models\StudyPlanDetail>
     */
    public function listByPlan(int $planId): Collection;

    /**
     * Tambah mata kuliah ke KRS.
     *
     * @param  array<string, mixed>  $data
     * @return \App\Models\StudyPlanDetail
     */
    public function add(array $data): \App\Models\StudyPlanDetail;

    /**
     * Hapus detail KRS.
     */
    public function remove(int $detailId): void;

    /**
     * Cari detail KRS berdasarkan id.
     *
     * @return \App\Models\StudyPlanDetail|null
     */
    public function findById(int $detailId): ?\App\Models\StudyPlanDetail;
}
