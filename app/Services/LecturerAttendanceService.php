<?php

namespace App\Services;

use App\DTO\LecturerAttendanceData;
use App\Enums\LecturerAttendanceStatus;
use App\Models\CourseOffering;
use App\Models\LecturerAttendance;
use App\Repositories\Contracts\CourseOfferingRepository;
use App\Repositories\Contracts\LecturerAttendanceRepository;
use Illuminate\Support\Collection;

class LecturerAttendanceService
{
    public function __construct(
        private readonly LecturerAttendanceRepository $attendanceRepo,
        private readonly CourseOfferingRepository $offeringRepo,
    ) {}

    /**
     * Data halaman kehadiran dosen.
     *
     * @return array{offerings: Collection, attendances: Collection}
     */
    public function pageData(int $lecturerId): array
    {
        $offerings = $this->offeringRepo->listByLecturer($lecturerId);
        $attendances = $this->attendanceRepo->listForLecturer($lecturerId);

        return [
            'offerings' => $offerings,
            'attendances' => $attendances,
        ];
    }

    /**
     * Check-in: catat jam masuk.
     */
    public function checkIn(int $lecturerId, LecturerAttendanceData $data): LecturerAttendance
    {
        // Pastikan dosen mengajar kelas ini
        $offering = $this->offeringRepo->findById($data->course_offering_id);

        if (! $offering || (int) $offering->lecturer_id !== $lecturerId) {
            abort(403, 'Anda tidak mengajar kelas ini.');
        }

        // Cek apakah sudah ada record untuk offering + tanggal ini
        $existing = $this->attendanceRepo->findByOfferingAndDate(
            $data->course_offering_id,
            $data->date,
        );

        if ($existing) {
            abort(409, 'Anda sudah melakukan check-in untuk kelas ini pada tanggal tersebut.');
        }

        // Tentukan status: terlambat jika check_in > jam mulai
        $status = $this->determineStatus($data->check_in, $offering);

        return $this->attendanceRepo->create([
            'lecturer_id' => $lecturerId,
            'course_offering_id' => $data->course_offering_id,
            'date' => $data->date,
            'check_in' => $data->check_in,
            'status' => $status->value,
        ]);
    }

    /**
     * Check-out: catat jam keluar.
     */
    public function checkOut(int $lecturerId, int $attendanceId): void
    {
        $record = LecturerAttendance::query()
            ->select(['id', 'lecturer_id', 'check_in', 'check_out'])
            ->findOrFail($attendanceId);

        if ((int) $record->lecturer_id !== $lecturerId) {
            abort(403, 'Anda tidak memiliki akses ke record ini.');
        }

        if ($record->check_out) {
            abort(409, 'Anda sudah melakukan check-out.');
        }

        $now = now()->format('H:i:s');

        $this->attendanceRepo->update($attendanceId, [
            'check_out' => $now,
        ]);
    }

    /**
     * Tentukan status hadir/terlambat berdasarkan jam check-in vs jadwal.
     */
    private function determineStatus(?string $checkIn, CourseOffering $offering): LecturerAttendanceStatus
    {
        if (! $checkIn || ! $offering->start_time) {
            return LecturerAttendanceStatus::Hadir;
        }

        $checkInTime = strtotime($checkIn);
        $startTime = strtotime($offering->start_time);

        // Terlambat jika check-in > 15 menit dari jam mulai
        if ($checkInTime > ($startTime + 900)) {
            return LecturerAttendanceStatus::Terlambat;
        }

        return LecturerAttendanceStatus::Hadir;
    }

    /**
     * Rekap kehadiran per dosen dengan filter.
     *
     * @param  array{study_program_id?: int, semester_id?: int, lecturer_id?: int}  $filters
     * @return array{summary: Collection, stats: array{total_lecturers: int, avg_percentage: float}}
     */
    public function summary(array $filters): array
    {
        $summary = $this->attendanceRepo->summary($filters);

        $totalLecturers = $summary->count();
        $avgPercentage = $totalLecturers > 0
            ? round($summary->avg(function ($item) {
                $total = (int) $item->total_pertemuan;
                if ($total === 0) return 0;
                $hadir = (int) $item->total_hadir + (int) $item->total_terlambat;

                return round(($hadir / $total) * 100, 2);
            }), 2)
            : 0;

        // Format summary dengan persentase
        $formatted = $summary->map(function ($item) {
            $total = (int) $item->total_pertemuan;
            $hadir = (int) $item->total_hadir + (int) $item->total_terlambat;
            $percentage = $total > 0 ? round(($hadir / $total) * 100, 2) : 0;

            return [
                'lecturer_id' => $item->lecturer_id,
                'nidn' => $item->lecturer?->nidn,
                'lecturer_name' => $item->lecturer?->user?->name,
                'study_program' => $item->lecturer?->studyProgram?->name,
                'total_pertemuan' => $total,
                'total_hadir' => (int) $item->total_hadir,
                'total_terlambat' => (int) $item->total_terlambat,
                'total_izin' => (int) $item->total_izin,
                'total_alpha' => (int) $item->total_alpha,
                'persentase' => $percentage,
            ];
        });

        return [
            'summary' => $formatted,
            'stats' => [
                'total_lecturers' => $totalLecturers,
                'avg_percentage' => $avgPercentage,
            ],
        ];
    }

    /**
     * Detail riwayat kehadiran seorang dosen.
     *
     * @return array<int, array>
     */
    public function detail(int $lecturerId, int $semesterId): array
    {
        $records = $this->attendanceRepo->detail($lecturerId, $semesterId);

        return $records->map(function ($record) {
            return [
                'id' => $record->id,
                'date' => $record->date?->format('Y-m-d'),
                'course_code' => $record->courseOffering?->course?->code,
                'course_name' => $record->courseOffering?->course?->name,
                'check_in' => $record->check_in,
                'check_out' => $record->check_out,
                'status' => $record->status?->value,
            ];
        })->all();
    }
}
