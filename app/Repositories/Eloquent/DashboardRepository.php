<?php

namespace App\Repositories\Eloquent;

use App\Enums\DayOfWeek;
use App\Enums\StudyPlanStatus;
use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Grade;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\StudyProgram;
use App\Models\User;
use App\Repositories\Contracts\DashboardRepository as DashboardRepositoryContract;

class DashboardRepository implements DashboardRepositoryContract
{
    public function superAdminStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_lecturers' => Lecturer::count(),
            'total_students' => Student::count(),
            'total_courses' => Course::count(),
            'recent_activities' => ActivityLog::query()
                ->select(['id', 'user_id', 'action', 'model_type', 'created_at'])
                ->with('user:id,name')
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn (ActivityLog $log) => [
                    'id' => $log->id,
                    'user' => $log->user?->name,
                    'action' => $log->action,
                    'model_type' => class_basename($log->model_type),
                    'created_at' => $log->created_at?->toDateTimeString(),
                ]),
        ];
    }

    public function kaprodiStats(int $studyProgramId): array
    {
        return [
            'total_courses' => Course::where('study_program_id', $studyProgramId)->count(),
            'total_offerings' => CourseOffering::query()
                ->whereHas('course', fn ($q) => $q->where('study_program_id', $studyProgramId))
                ->count(),
            'total_students' => Student::where('study_program_id', $studyProgramId)->count(),
            'courses' => Course::query()
                ->select(['id', 'code', 'name', 'sks', 'semester', 'type'])
                ->where('study_program_id', $studyProgramId)
                ->orderBy('code')
                ->get()
                ->map(fn (Course $course) => [
                    'id' => $course->id,
                    'code' => $course->code,
                    'name' => $course->name,
                    'sks' => $course->sks,
                    'semester' => $course->semester,
                    'type' => $course->type?->value,
                ]),
        ];
    }

    public function dosenStats(int $lecturerId): array
    {
        $offeringIds = CourseOffering::where('lecturer_id', $lecturerId)->pluck('id');

        return [
            'total_offerings' => $offeringIds->count(),
            'total_students' => StudyPlanDetail::query()
                ->whereIn('course_offering_id', $offeringIds)
                ->distinct()
                ->count('student_id'),
            'unscored_submissions' => \App\Models\AssignmentSubmission::query()
                ->whereHas('assignment', fn ($q) => $q->whereIn('course_offering_id', $offeringIds))
                ->whereNull('score')
                ->count(),
            'today_schedule' => $this->scheduleFor(
                fn ($query) => $query->where('lecturer_id', $lecturerId),
            ),
        ];
    }

    public function mahasiswaStats(int $studentId): array
    {
        $plan = StudyPlan::query()
            ->where('student_id', $studentId)
            ->where('status', StudyPlanStatus::Approved)
            ->latest()
            ->first();

        $offeringIds = $plan?->studyPlanDetails()->pluck('course_offering_id')->all() ?? [];

        $totalSks = CourseOffering::query()
            ->whereIn('id', $offeringIds)
            ->with('course:id,sks')
            ->get()
            ->sum(fn (CourseOffering $offering) => $offering->course?->sks ?? 0);

        $averageGrade = Grade::where('student_id', $studentId)->avg('score');

        return [
            'total_sks' => $totalSks,
            'total_classes' => count($offeringIds),
            'average_grade' => $averageGrade !== null ? round((float) $averageGrade, 2) : null,
            'today_schedule' => $this->scheduleFor(
                fn ($query) => $query->whereIn('id', $offeringIds),
            ),
        ];
    }

    public function pimpinanStats(): array
    {
        return [
            'total_students' => Student::count(),
            'average_gpa' => round((float) (Student::avg('gpa') ?? 0), 2),
            'total_lecturers' => Lecturer::count(),
            'total_programs' => StudyProgram::count(),
            'grade_distribution' => Grade::query()
                ->select(['letter_grade'])
                ->selectRaw('COUNT(*) as total')
                ->groupBy('letter_grade')
                ->orderBy('letter_grade')
                ->get()
                ->map(fn (Grade $grade) => [
                    'letter' => $grade->letter_grade?->value,
                    'total' => (int) $grade->total,
                ]),
            'gpa_trend' => Student::query()
                ->select(['entry_year'])
                ->selectRaw('AVG(gpa) as average')
                ->groupBy('entry_year')
                ->orderBy('entry_year')
                ->get()
                ->map(fn (Student $student) => [
                    'year' => $student->entry_year,
                    'average' => round((float) $student->average, 2),
                ]),
        ];
    }

    /**
     * Jadwal perkuliahan hari ini dengan kolom spesifik.
     *
     * @param  callable  $scope
     */
    private function scheduleFor(callable $scope): array
    {
        $day = $this->today();

        if (! $day) {
            return [];
        }

        return CourseOffering::query()
            ->select(['id', 'course_id', 'classroom_id', 'start_time', 'end_time'])
            ->with(['course:id,code,name', 'classroom:id,name'])
            ->where('day', $day)
            ->where($scope)
            ->orderBy('start_time')
            ->get()
            ->map(fn (CourseOffering $offering) => [
                'course' => $offering->course?->name,
                'classroom' => $offering->classroom?->name,
                'start_time' => $offering->start_time,
                'end_time' => $offering->end_time,
            ])
            ->all();
    }

    /**
     * Konversi hari ini (Bahasa Inggris) ke enum DayOfWeek.
     */
    private function today(): ?DayOfWeek
    {
        return match (strtolower(now()->englishDayOfWeek)) {
            'monday' => DayOfWeek::Senin,
            'tuesday' => DayOfWeek::Selasa,
            'wednesday' => DayOfWeek::Rabu,
            'thursday' => DayOfWeek::Kamis,
            'friday' => DayOfWeek::Jumat,
            'saturday' => DayOfWeek::Sabtu,
            'sunday' => DayOfWeek::Minggu,
            default => null,
        };
    }
}
