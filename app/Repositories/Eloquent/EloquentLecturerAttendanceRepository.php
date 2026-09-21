<?php

namespace App\Repositories\Eloquent;

use App\Models\Lecturer;
use App\Models\LecturerAttendance;
use App\Repositories\Contracts\LecturerAttendanceRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentLecturerAttendanceRepository implements LecturerAttendanceRepository
{
    public function listForLecturer(int $lecturerId): Collection
    {
        return LecturerAttendance::query()
            ->select(['id', 'lecturer_id', 'course_offering_id', 'date', 'check_in', 'check_out', 'status'])
            ->where('lecturer_id', $lecturerId)
            ->with([
                'courseOffering:id,course_id,class',
                'courseOffering.course:id,code,name',
            ])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByOfferingAndDate(int $offeringId, string $date): ?LecturerAttendance
    {
        return LecturerAttendance::query()
            ->select(['id', 'lecturer_id', 'course_offering_id', 'date', 'check_in', 'check_out', 'status'])
            ->where('course_offering_id', $offeringId)
            ->where('date', $date)
            ->first();
    }

    public function create(array $data): LecturerAttendance
    {
        return LecturerAttendance::query()->create($data);
    }

    public function update(int $id, array $data): void
    {
        LecturerAttendance::query()->where('id', $id)->update($data);
    }

    public function summary(array $filters): Collection
    {
        $query = LecturerAttendance::query()
            ->select([
                'lecturer_id',
                DB::raw('COUNT(*) as total_pertemuan'),
                DB::raw("SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as total_hadir"),
                DB::raw("SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END) as total_terlambat"),
                DB::raw("SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as total_izin"),
                DB::raw("SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as total_alpha"),
            ])
            ->with([
                'lecturer:id,user_id,study_program_id,nidn',
                'lecturer.user:id,name',
                'lecturer.studyProgram:id,name',
            ]);

        if (! empty($filters['semester_id'])) {
            $query->whereHas('courseOffering', fn ($q) => $q->where('semester_id', $filters['semester_id']));
        }

        if (! empty($filters['study_program_id'])) {
            $query->whereHas('lecturer', fn ($q) => $q->where('study_program_id', $filters['study_program_id']));
        }

        if (! empty($filters['lecturer_id'])) {
            $query->where('lecturer_id', $filters['lecturer_id']);
        }

        return $query->groupBy('lecturer_id')
            ->orderBy('lecturer_id')
            ->get();
    }

    public function detail(int $lecturerId, int $semesterId): Collection
    {
        return LecturerAttendance::query()
            ->select(['id', 'lecturer_id', 'course_offering_id', 'date', 'check_in', 'check_out', 'status'])
            ->with([
                'courseOffering:id,course_id,semester_id',
                'courseOffering.course:id,code,name',
            ])
            ->where('lecturer_id', $lecturerId)
            ->whereHas('courseOffering', fn ($q) => $q->where('semester_id', $semesterId))
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get();
    }
}
