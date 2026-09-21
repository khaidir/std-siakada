<?php

namespace App\Services;

use App\Enums\StudyPlanDetailStatus;
use App\Enums\StudyPlanStatus;
use App\Models\CourseOffering;
use App\Models\StudyPlan;
use App\Models\User;
use App\Repositories\Contracts\CourseOfferingRepository;
use App\Repositories\Contracts\StudyPlanDetailRepository;
use App\Repositories\Contracts\StudyPlanRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KrsService
{
    public function __construct(
        private readonly StudyPlanRepository $plans,
        private readonly StudyPlanDetailRepository $details,
        private readonly CourseOfferingRepository $offerings,
    ) {}

    /**
     * Data halaman KRS: info semester, daftar kelas tersedia, dan pilihan saat ini.
     *
     * @return array<string, mixed>
     */
    public function pageData(User $user): array
    {
        $studentId = (int) $user->student?->id;
        $semester = $this->activeSemester();

        if (! $semester) {
            return [
                'semester' => null,
                'plan' => null,
                'offerings' => [],
                'selected' => [],
                'sks_limit' => 0,
                'total_sks' => 0,
            ];
        }

        $plan = $this->plans->activeForStudent($studentId, (int) $semester['id']);

        if (! $plan) {
            $plan = $this->plans->createForStudent($studentId, (int) $semester['id']);
        }

        $selected = $this->details->listByPlan((int) $plan->id);
        $totalSks = $this->sumSks($selected);
        $sksLimit = $this->sksLimit($user);

        $offerings = $this->offerings->availableForStudent($studentId, (int) $semester['id']);

        return [
            'semester' => $semester,
            'plan' => [
                'id' => $plan->id,
                'status' => $plan->status?->value,
                'can_edit' => $plan->status === StudyPlanStatus::Draft,
            ],
            'offerings' => $this->formatOfferings($offerings, $selected),
            'selected' => $this->formatSelected($selected),
            'sks_limit' => $sksLimit,
            'total_sks' => $totalSks,
        ];
    }

    /**
     * Tambah mata kuliah ke KRS.
     */
    public function addCourse(User $user, int $offeringId): void
    {
        $studentId = (int) $user->student?->id;
        $semester = $this->activeSemester();

        if (! $semester) {
            throw ValidationException::withMessages([
                'offering' => 'Tidak ada semester aktif.',
            ]);
        }

        $plan = $this->plans->activeForStudent($studentId, (int) $semester['id']);

        if (! $plan) {
            $plan = $this->plans->createForStudent($studentId, (int) $semester['id']);
        }

        if ($plan->status !== StudyPlanStatus::Draft) {
            throw ValidationException::withMessages([
                'offering' => 'KRS sudah tidak dalam status draft.',
            ]);
        }

        // Validasi offering tersedia untuk mahasiswa ini.
        $offerings = $this->offerings->availableForStudent($studentId, (int) $semester['id']);
        $offering = $offerings->firstWhere('id', $offeringId);

        if (! $offering) {
            throw ValidationException::withMessages([
                'offering' => 'Mata kuliah tidak tersedia.',
            ]);
        }

        DB::transaction(function () use ($plan, $offering, $studentId, $user) {
            // Cek duplikat.
            $existing = $this->details->listByPlan((int) $plan->id);
            $alreadyAdded = $existing->firstWhere('course_offering_id', $offering->id);

            if ($alreadyAdded) {
                throw ValidationException::withMessages([
                    'offering' => 'Mata kuliah sudah ditambahkan ke KRS.',
                ]);
            }

            // Cek kuota.
            $approvedCount = $this->details->listByPlan((int) $plan->id)
                ->where('status', StudyPlanDetailStatus::Approved)
                ->count();

            if ($offering->quota && $approvedCount >= $offering->quota) {
                throw ValidationException::withMessages([
                    'offering' => 'Kuota kelas sudah penuh.',
                ]);
            }

            // Cek jadwal bentrok.
            $this->checkScheduleConflict($existing, $offering);

            // Cek batas SKS.
            $currentSks = $this->sumSks($existing);
            $offeringSks = (int) ($offering->course?->sks ?? 0);
            $sksLimit = $this->sksLimit($user);

            if (($currentSks + $offeringSks) > $sksLimit) {
                throw ValidationException::withMessages([
                    'offering' => "Melebihi batas SKS ({$sksLimit}).",
                ]);
            }

            $this->details->add([
                'study_plan_id' => $plan->id,
                'course_offering_id' => $offering->id,
                'status' => 'pending',
            ]);
        });
    }

    /**
     * Hapus mata kuliah dari KRS.
     */
    public function removeCourse(User $user, int $detailId): void
    {
        $studentId = (int) $user->student?->id;
        $semester = $this->activeSemester();

        if (! $semester) {
            throw ValidationException::withMessages([
                'detail' => 'Tidak ada semester aktif.',
            ]);
        }

        $plan = $this->plans->activeForStudent($studentId, (int) $semester['id']);

        if (! $plan) {
            throw ValidationException::withMessages([
                'detail' => 'KRS tidak ditemukan.',
            ]);
        }

        if ($plan->status !== StudyPlanStatus::Draft) {
            throw ValidationException::withMessages([
                'detail' => 'KRS sudah tidak dalam status draft.',
            ]);
        }

        $detail = $this->details->findById($detailId);

        if (! $detail || (int) $detail->study_plan_id !== (int) $plan->id) {
            throw ValidationException::withMessages([
                'detail' => 'Detail KRS tidak ditemukan.',
            ]);
        }

        $this->details->remove($detailId);
    }

    /**
     * Submit KRS (ubah status dari draft ke submitted).
     */
    public function submit(User $user): void
    {
        $studentId = (int) $user->student?->id;
        $semester = $this->activeSemester();

        if (! $semester) {
            throw ValidationException::withMessages([
                'plan' => 'Tidak ada semester aktif.',
            ]);
        }

        $plan = $this->plans->activeForStudent($studentId, (int) $semester['id']);

        if (! $plan) {
            throw ValidationException::withMessages([
                'plan' => 'KRS tidak ditemukan.',
            ]);
        }

        if ($plan->status !== StudyPlanStatus::Draft) {
            throw ValidationException::withMessages([
                'plan' => 'KRS sudah pernah disubmit.',
            ]);
        }

        $selected = $this->details->listByPlan((int) $plan->id);

        if ($selected->isEmpty()) {
            throw ValidationException::withMessages([
                'plan' => 'Belum ada mata kuliah yang dipilih.',
            ]);
        }

        $plan->status = StudyPlanStatus::Submitted;
        $this->plans->save($plan);
    }

    /**
     * Semester aktif saat ini.
     *
     * @return array<string, mixed>|null
     */
    private function activeSemester(): ?array
    {
        $semester = \App\Models\Semester::query()
            ->select(['id', 'academic_year_id', 'type', 'start_date', 'end_date'])
            ->with('academicYear:id,name')
            ->where('is_active', true)
            ->first();

        if (! $semester) {
            return null;
        }

        return [
            'id' => $semester->id,
            'type' => $semester->type?->value,
            'academic_year' => $semester->academicYear?->name,
        ];
    }

    /**
     * Batas SKS berdasarkan IPK.
     */
    private function sksLimit(User $user): int
    {
        $gpa = (float) ($user->student?->gpa ?? 0);

        return match (true) {
            $gpa >= 3.00 => 24,
            $gpa >= 2.50 => 21,
            $gpa >= 2.00 => 18,
            default => 15,
        };
    }

    /**
     * Cek jadwal bentrok.
     *
     * @param  Collection<int, \App\Models\StudyPlanDetail>  $existing
     */
    private function checkScheduleConflict(Collection $existing, CourseOffering $offering): void
    {
        foreach ($existing as $detail) {
            $existingOffering = $detail->courseOffering;

            if (! $existingOffering) {
                continue;
            }

            if ($existingOffering->day === $offering->day
                && $existingOffering->start_time <= $offering->end_time
                && $existingOffering->end_time >= $offering->start_time) {
                throw ValidationException::withMessages([
                    'offering' => 'Jadwal bentrok dengan mata kuliah lain.',
                ]);
            }
        }
    }

    /**
     * Hitung total SKS dari detail KRS.
     *
     * @param  Collection<int, \App\Models\StudyPlanDetail>  $details
     */
    private function sumSks(Collection $details): int
    {
        return $details->sum(fn ($d) => (int) ($d->courseOffering?->course?->sks ?? 0));
    }

    /**
     * Format daftar kelas tersedia.
     *
     * @param  Collection<int, \App\Models\CourseOffering>  $offerings
     * @param  Collection<int, \App\Models\StudyPlanDetail>  $selected
     * @return array<int, array<string, mixed>>
     */
    private function formatOfferings(Collection $offerings, Collection $selected): array
    {
        $selectedOfferingIds = $selected->pluck('course_offering_id')->all();

        return $offerings
            ->map(fn (CourseOffering $o) => [
                'id' => $o->id,
                'course_name' => $o->course?->name,
                'course_code' => $o->course?->code,
                'sks' => $o->course?->sks,
                'lecturer' => $o->lecturer?->user?->name,
                'day' => $o->day?->value,
                'start_time' => $o->start_time,
                'end_time' => $o->end_time,
                'classroom' => $o->classroom?->name,
                'quota' => $o->quota,
                'is_selected' => in_array($o->id, $selectedOfferingIds, true),
            ])
            ->values()
            ->all();
    }

    /**
     * Format daftar pilihan saat ini.
     *
     * @param  Collection<int, \App\Models\StudyPlanDetail>  $selected
     * @return array<int, array<string, mixed>>
     */
    private function formatSelected(Collection $selected): array
    {
        return $selected
            ->map(fn ($d) => [
                'id' => $d->id,
                'course_offering_id' => $d->course_offering_id,
                'course_name' => $d->courseOffering?->course?->name,
                'course_code' => $d->courseOffering?->course?->code,
                'sks' => $d->courseOffering?->course?->sks,
                'lecturer' => $d->courseOffering?->lecturer?->user?->name,
                'day' => $d->courseOffering?->day?->value,
                'start_time' => $d->courseOffering?->start_time,
                'end_time' => $d->courseOffering?->end_time,
                'classroom' => $d->courseOffering?->classroom?->name,
                'status' => $d->status?->value,
            ])
            ->all();
    }
}
