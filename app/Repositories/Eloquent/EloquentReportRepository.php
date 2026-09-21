<?php

namespace App\Repositories\Eloquent;

use App\Models\CourseOffering;
use App\Models\Grade;
use App\Models\Lecturer;
use App\Models\LecturerAttendance;
use App\Models\Student;
use App\Models\StudyPlanDetail;
use App\Repositories\Contracts\ReportRepository as ReportRepositoryContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentReportRepository implements ReportRepositoryContract
{
    public function studentStats(): array
    {
        return [
            'total' => (int) Student::count(),
            'avg_gpa' => round((float) (Student::avg('gpa') ?? 0), 2),
        ];
    }

    public function gradeDistribution(?int $semesterId = null): Collection
    {
        $query = Grade::query()
            ->select(['letter_grade'])
            ->selectRaw('COUNT(*) as total');

        if ($semesterId !== null) {
            $query->whereHas('courseOffering', fn ($q) => $q->where('semester_id', $semesterId));
        }

        return $query->groupBy('letter_grade')
            ->orderBy('letter_grade')
            ->get()
            ->map(fn (Grade $grade) => [
                'letter' => $grade->letter_grade?->value ?? $grade->letter_grade,
                'total' => (int) $grade->total,
            ]);
    }

    public function lecturerWorkload(int $semesterId): Collection
    {
        return Lecturer::query()
            ->select([
                'lecturers.id as lecturer_id',
                'users.name as lecturer_name',
                'lecturers.nidn',
            ])
            ->selectRaw('COUNT(DISTINCT course_offerings.id) as total_classes')
            ->selectRaw('COALESCE(SUM(courses.sks), 0) as total_sks')
            ->join('users', 'lecturers.user_id', '=', 'users.id')
            ->leftJoin('course_offerings', function ($join) use ($semesterId) {
                $join->on('lecturers.id', '=', 'course_offerings.lecturer_id')
                    ->where('course_offerings.semester_id', '=', $semesterId);
            })
            ->leftJoin('courses', 'course_offerings.course_id', '=', 'courses.id')
            ->groupBy('lecturers.id', 'lecturers.nidn', 'users.name')
            ->orderBy('users.name')
            ->get()
            ->map(fn ($row) => [
                'lecturer_id' => (int) $row->lecturer_id,
                'lecturer_name' => $row->lecturer_name,
                'nidn' => $row->nidn,
                'total_classes' => (int) $row->total_classes,
                'total_sks' => (int) $row->total_sks,
            ]);
    }

    public function lecturerAttendance(int $semesterId): Collection
    {
        $offeringIds = CourseOffering::where('semester_id', $semesterId)->pluck('id');

        return Lecturer::query()
            ->select([
                'lecturers.id as lecturer_id',
                'users.name as lecturer_name',
                'lecturers.nidn',
            ])
            ->selectRaw('COUNT(DISTINCT lecturer_attendances.id) as total_sessions')
            ->selectRaw("SUM(CASE WHEN lecturer_attendances.status IN ('hadir', 'terlambat') THEN 1 ELSE 0 END) as attended_sessions")
            ->join('users', 'lecturers.user_id', '=', 'users.id')
            ->leftJoin('lecturer_attendances', function ($join) use ($offeringIds) {
                $join->on('lecturers.id', '=', 'lecturer_attendances.lecturer_id')
                    ->whereIn('lecturer_attendances.course_offering_id', $offeringIds);
            })
            ->groupBy('lecturers.id', 'lecturers.nidn', 'users.name')
            ->orderBy('users.name')
            ->get()
            ->map(fn ($row) => [
                'lecturer_id' => (int) $row->lecturer_id,
                'lecturer_name' => $row->lecturer_name,
                'nidn' => $row->nidn,
                'total_sessions' => (int) $row->total_sessions,
                'attended_sessions' => (int) $row->attended_sessions,
                'attendance_percentage' => $row->total_sessions > 0
                    ? round(((int) $row->attended_sessions / (int) $row->total_sessions) * 100, 2)
                    : 0.0,
            ]);
    }

    public function studentGradeReports(?int $semesterId = null, ?int $studyProgramId = null): Collection
    {
        $query = Student::query()
            ->select([
                'students.id as student_id',
                'students.nim',
                'users.name as student_name',
                'study_programs.name as study_program',
            ])
            ->selectRaw('COALESCE(SUM(CASE WHEN grades.id IS NOT NULL THEN courses.sks ELSE 0 END), 0) as total_sks')
            ->selectRaw('COALESCE(AVG(grades.grade_point), 0) as gpa')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->join('study_programs', 'students.study_program_id', '=', 'study_programs.id')
            ->leftJoin('study_plans', function ($join) use ($semesterId) {
                $join->on('students.id', '=', 'study_plans.student_id')
                    ->where('study_plans.semester_id', '=', $semesterId);
            })
            ->leftJoin('study_plan_details', 'study_plans.id', '=', 'study_plan_details.study_plan_id')
            ->leftJoin('course_offerings', 'study_plan_details.course_offering_id', '=', 'course_offerings.id')
            ->leftJoin('courses', 'course_offerings.course_id', '=', 'courses.id')
            ->leftJoin('grades', 'study_plan_details.id', '=', 'grades.study_plan_detail_id')
            ->groupBy('students.id', 'students.nim', 'users.name', 'study_programs.name');

        if ($studyProgramId !== null) {
            $query->where('students.study_program_id', $studyProgramId);
        }

        return $query->orderBy('users.name')
            ->get()
            ->map(fn ($row) => [
                'student_id' => (int) $row->student_id,
                'nim' => $row->nim,
                'student_name' => $row->student_name,
                'study_program' => $row->study_program,
                'total_sks' => (int) $row->total_sks,
                'gpa' => round((float) $row->gpa, 2),
            ]);
    }

    public function studentAttendance(?int $semesterId = null, ?int $studyProgramId = null): Collection
    {
        $query = Student::query()
            ->select([
                'students.id as student_id',
                'students.nim',
                'users.name as student_name',
            ])
            ->selectRaw('COUNT(DISTINCT attendances.id) as total_sessions')
            ->selectRaw("SUM(CASE WHEN attendances.status IN ('hadir', 'izin') THEN 1 ELSE 0 END) as attended")
            ->join('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('attendances', function ($join) use ($semesterId) {
                $join->on('students.id', '=', 'attendances.student_id')
                    ->whereHas('courseOffering', fn ($q) => $q->where('semester_id', $semesterId));
            })
            ->groupBy('students.id', 'students.nim', 'users.name');

        if ($studyProgramId !== null) {
            $query->where('students.study_program_id', $studyProgramId);
        }

        return $query->orderBy('users.name')
            ->get()
            ->map(fn ($row) => [
                'student_id' => (int) $row->student_id,
                'nim' => $row->nim,
                'student_name' => $row->student_name,
                'total_sessions' => (int) $row->total_sessions,
                'attended' => (int) $row->attended,
                'percentage' => $row->total_sessions > 0
                    ? round(((int) $row->attended / (int) $row->total_sessions) * 100, 2)
                    : 0.0,
            ]);
    }
}
