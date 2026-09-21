<?php

namespace App\Services;

use App\Enums\StudyPlanStatus;
use App\Models\CourseMaterial;
use App\Models\CourseOffering;
use App\Models\StudyPlan;
use App\Models\User;
use App\Repositories\Contracts\MaterialRepository;
use Illuminate\Support\Facades\Storage;

class MahasiswaMaterialService
{
    public function __construct(
        private readonly MaterialRepository $materials,
    ) {}

    /**
     * Data halaman materi untuk mahasiswa.
     *
     * @return array<string, mixed>
     */
    public function pageData(User $user, ?int $offeringId = null): array
    {
        $studentId = (int) $user->student?->id;

        // Ambil KRS approved milik mahasiswa.
        $plan = StudyPlan::query()
            ->select(['id', 'student_id', 'semester_id', 'status'])
            ->where('student_id', $studentId)
            ->where('status', StudyPlanStatus::Approved)
            ->latest()
            ->first();

        if (! $plan) {
            return [
                'offerings' => [],
                'materials' => [],
                'selected_offering_id' => null,
            ];
        }

        // Ambil offering yang diikuti mahasiswa (dari KRS approved).
        $offeringIds = $plan->studyPlanDetails()
            ->select(['course_offering_id'])
            ->where('status', 'approved')
            ->pluck('course_offering_id')
            ->all();

        $offerings = CourseOffering::query()
            ->select(['id', 'course_id', 'lecturer_id', 'day', 'start_time', 'end_time'])
            ->with([
                'course:id,code,name,sks',
                'lecturer:id,user_id',
                'lecturer.user:id,name',
            ])
            ->whereIn('id', $offeringIds)
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        $offeringOptions = $offerings
            ->map(fn (CourseOffering $o) => [
                'id' => $o->id,
                'label' => trim(sprintf(
                    '%s (%s) · %s',
                    $o->course?->name,
                    $o->course?->code,
                    $o->day?->value ?? '',
                )),
            ])
            ->values()
            ->all();

        // Pastikan offering yang dipilih adalah milik mahasiswa.
        $validOfferingIds = $offerings->pluck('id')->all();
        $selected = $offeringId !== null && in_array($offeringId, $validOfferingIds, true)
            ? $offeringId
            : (int) ($offerings->first()?->id ?? 0);

        return [
            'offerings' => $offeringOptions,
            'materials' => $selected > 0 ? $this->materialsData($selected) : [],
            'selected_offering_id' => $selected,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function materialsData(int $offeringId): array
    {
        return $this->materials->listByOffering($offeringId)
            ->map(fn (CourseMaterial $m) => [
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
