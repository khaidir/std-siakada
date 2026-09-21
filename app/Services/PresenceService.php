<?php

namespace App\Services;

use App\Repositories\Contracts\AttendanceRepository;
use App\Repositories\Contracts\CourseOfferingRepository;
use App\Repositories\Contracts\StudyPlanRepository;
use App\Models\Semester;
use Illuminate\Support\Collection;

class PresenceService
{
    public function __construct(
        protected CourseOfferingRepository $courseOfferings,
        protected AttendanceRepository $attendances,
        protected StudyPlanRepository $studyPlans,
    ) {}

    /**
     * Cari semester aktif.
     */
    private function activeSemester(): ?Semester
    {
        return Semester::query()
            ->select(['id', 'academic_year_id', 'type'])
            ->with('academicYear:id,code')
            ->where('is_active', true)
            ->first();
    }

    /**
     * Data halaman jadwal kuliah mahasiswa.
     *
     * @return array{schedules: Collection, semesterLabel: ?string}
     */
    public function scheduleData(int $studentId): array
    {
        $semester = $this->activeSemester();

        if (! $semester) {
            return [
                'schedules' => collect(),
                'semesterLabel' => null,
            ];
        }

        $activePlan = $this->studyPlans->activeForStudent($studentId, $semester->id);

        if (! $activePlan) {
            return [
                'schedules' => collect(),
                'semesterLabel' => null,
            ];
        }

        $schedules = $this->courseOfferings->scheduleForStudent($studentId, $semester->id);

        $type = $semester->type?->value ?? '';
        $typeLabel = $type === 'ganjil' ? 'Ganjil' : ($type === 'genap' ? 'Genap' : 'Pendek');
        $semesterLabel = $semester->academicYear?->code ? "{$semester->academicYear->code} - {$typeLabel}" : null;

        return [
            'schedules' => $schedules,
            'semesterLabel' => $semesterLabel,
        ];
    }

    /**
     * Data halaman presensi mahasiswa.
     *
     * @return array{offerings: Collection, selectedOfferingId: ?int, attendances: Collection, percentage: float}
     */
    public function presenceData(int $studentId, ?int $offeringId = null): array
    {
        $semester = $this->activeSemester();

        $offerings = collect();
        if ($semester) {
            $offerings = $this->courseOfferings->scheduleForStudent($studentId, $semester->id);
        }

        $offeringsForSelect = $offerings->map(fn ($o) => [
            'id' => $o->id,
            'label' => trim("{$o->course?->name} ({$o->course?->code})"),
        ])->values();

        $selected = $offeringId ?? $offerings->first()?->id;

        $attendances = collect();
        $percentage = 0.0;

        if ($selected) {
            $attendances = $this->attendances->listForStudent($studentId, $selected);
            $percentage = $this->attendancePercentage($attendances);
        }

        return [
            'offerings' => $offeringsForSelect,
            'selectedOfferingId' => $selected,
            'attendances' => $attendances,
            'percentage' => $percentage,
        ];
    }

    /**
     * Hitung persentase kehadiran.
     */
    public function attendancePercentage(Collection $attendances): float
    {
        $total = $attendances->count();

        if ($total === 0) {
            return 0.0;
        }

        $hadir = $attendances->filter(fn ($a) => $a->status === \App\Enums\AttendanceStatus::Hadir)->count();

        return round(($hadir / $total) * 100, 2);
    }
}
