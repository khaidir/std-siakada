<?php

namespace App\Services;

use App\DTO\BatchAttendanceData;
use App\Models\Attendance;
use App\Models\CourseOffering;
use App\Models\StudyPlanDetail;
use App\Models\User;
use App\Repositories\Contracts\AttendanceRepository;
use App\Repositories\Contracts\CourseOfferingRepository;
use App\Repositories\Contracts\StudyPlanDetailRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function __construct(
        private readonly AttendanceRepository $attendances,
        private readonly CourseOfferingRepository $offerings,
        private readonly StudyPlanDetailRepository $details,
    ) {}

    /**
     * Data halaman presensi: kelas diampu, mahasiswa, dan rekap pertemuan.
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
            'students' => $selected > 0 ? $this->studentsData($selected) : [],
            'recap' => $selected > 0 ? $this->recapData($selected) : [],
            'selected_offering_id' => $selected,
        ];
    }

    /**
     * Simpan presensi batch satu pertemuan dalam satu transaksi.
     */
    public function store(int $offeringId, BatchAttendanceData $data): void
    {
        DB::transaction(function () use ($offeringId, $data) {
            $validStudentIds = $this->details->listApprovedByOffering($offeringId)
                ->map(fn (StudyPlanDetail $d) => (int) $d->studyPlan?->student_id)
                ->unique()
                ->all();

            $rows = [];

            foreach ($data->attendances as $attendance) {
                if (! in_array($attendance->student_id, $validStudentIds, true)) {
                    throw ValidationException::withMessages([
                        'attendances' => 'Terdapat mahasiswa yang tidak terdaftar di kelas ini.',
                    ]);
                }

                $rows[] = [
                    'course_offering_id' => $offeringId,
                    'student_id' => $attendance->student_id,
                    'meeting_number' => $attendance->meeting_number,
                    'date' => $attendance->date,
                    'status' => $attendance->status,
                ];
            }

            $this->attendances->upsertBatch($rows);
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function studentsData(int $offeringId): array
    {
        return $this->details->listApprovedByOffering($offeringId)
            ->map(fn (StudyPlanDetail $detail) => [
                'student_id' => (int) ($detail->studyPlan?->student_id ?? 0),
                'nim' => $detail->studyPlan?->student?->nim,
                'name' => $detail->studyPlan?->student?->user?->name,
                'status' => 'hadir',
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function recapData(int $offeringId): array
    {
        $grouped = [];

        foreach ($this->attendances->recapByOffering($offeringId) as $attendance) {
            $key = $attendance->meeting_number . '|' . $attendance->date->toDateString();

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'meeting_number' => (int) $attendance->meeting_number,
                    'date' => $attendance->date->toDateString(),
                    'hadir' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'alpha' => 0,
                    'total' => 0,
                ];
            }

            $status = $attendance->status->value;
            $grouped[$key][$status] = ($grouped[$key][$status] ?? 0) + 1;
            $grouped[$key]['total'] += 1;
        }

        return array_values($grouped);
    }
}
