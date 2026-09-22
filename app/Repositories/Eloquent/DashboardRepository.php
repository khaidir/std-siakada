<?php

namespace App\Repositories\Eloquent;

use App\Enums\AttendanceStatus;
use App\Enums\DayOfWeek;
use App\Enums\StudyPlanStatus;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Grade;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\StudyProgram;
use App\Models\Thesis;
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
            'announcement' => $this->announcementForStudent(),
            'khs' => $this->gradeDistribution($studentId),
            'grades' => $this->recentGrades($studentId),
            'attendance' => $this->attendanceRates($studentId),
            'thesis' => $this->thesisSummary($studentId),
            'upcoming' => $this->upcomingClasses($offeringIds),
            'calendar_marks' => $this->classDates($studentId),
        ];
    }

    /**
     * Pengumuman terbaru yang ditujukan untuk mahasiswa (atau untuk semua peran).
     *
     * @return array<string, mixed>|null
     */
    private function announcementForStudent(): ?array
    {
        $announcement = Announcement::query()
            ->select(['id', 'title', 'content', 'target_role', 'published_at'])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(fn ($query) => $query->whereNull('target_role')->orWhere('target_role', 'mahasiswa'))
            ->latest('published_at')
            ->first();

        if ($announcement === null) {
            return null;
        }

        return [
            'title' => $announcement->title,
            'body' => str($announcement->content)->limit(140)->toString(),
        ];
    }

    /**
     * Sebaran huruf mutu untuk donut KHS.
     *
     * @return array<int, array{label: string, value: int}>
     */
    private function gradeDistribution(int $studentId): array
    {
        return Grade::query()
            ->selectRaw('letter_grade, COUNT(*) as total')
            ->where('student_id', $studentId)
            ->groupBy('letter_grade')
            ->orderBy('letter_grade')
            ->get()
            ->map(fn ($row) => [
                // letter_grade di-cast ke enum GradeLetter, jadi dinormalkan ke string.
                'label' => $row->letter_grade instanceof \BackedEnum ? $row->letter_grade->value : (string) $row->letter_grade,
                'value' => (int) $row->total,
            ])
            ->all();
    }

    /**
     * Nilai terbaru untuk tabel Nilai Akademik.
     *
     * @return array<int, array<string, mixed>>
     */
    private function recentGrades(int $studentId): array
    {
        return Grade::query()
            ->select(['id', 'student_id', 'course_offering_id', 'score', 'letter_grade'])
            ->with('courseOffering:id,course_id', 'courseOffering.course:id,name,sks')
            ->where('student_id', $studentId)
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(fn (Grade $grade) => [
                'course' => $grade->courseOffering?->course?->name,
                'sks' => $grade->courseOffering?->course?->sks,
                'score' => $grade->score,
                'letter_grade' => $grade->letter_grade instanceof \BackedEnum ? $grade->letter_grade->value : (string) $grade->letter_grade,
            ])
            ->all();
    }

    /**
     * Persentase kehadiran per mata kuliah.
     *
     * @return array<int, array<string, mixed>>
     */
    private function attendanceRates(int $studentId): array
    {
        $rows = Attendance::query()
            ->selectRaw('course_offering_id, COUNT(*) as total, SUM(status = ?) as hadir', [AttendanceStatus::Hadir->value])
            ->where('student_id', $studentId)
            ->groupBy('course_offering_id')
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $offerings = CourseOffering::query()
            ->select(['id', 'course_id'])
            ->with('course:id,name')
            ->whereIn('id', $rows->pluck('course_offering_id'))
            ->get()
            ->keyBy('id');

        return $rows
            ->map(function ($row) use ($offerings) {
                $total = (int) $row->total;
                $hadir = (int) $row->hadir;

                return [
                    'course' => $offerings[$row->course_offering_id]?->course?->name ?? '-',
                    'percentage' => $total > 0 ? (int) round($hadir / $total * 100) : 0,
                    'sublabel' => "{$hadir} dari {$total} pertemuan",
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Ringkasan status TA/PA untuk stepper di dashboard.
     *
     * @return array<string, mixed>|null
     */
    private function thesisSummary(int $studentId): ?array
    {
        $thesis = Thesis::query()
            ->select(['id', 'student_id', 'title', 'status'])
            ->where('student_id', $studentId)
            ->first();

        if ($thesis === null) {
            return null;
        }

        return [
            'title' => $thesis->title,
            'status' => $thesis->status?->value ?? $thesis->status,
        ];
    }

    /**
     * Kelas terdekat hari ini, ditandai "Live" bila sedang berlangsung.
     *
     * @param  array<int, int>  $offeringIds
     * @return array<int, array<string, mixed>>
     */
    private function upcomingClasses(array $offeringIds): array
    {
        $now = now()->format('H:i:s');

        return collect($this->scheduleFor(fn ($query) => $query->whereIn('id', $offeringIds)))
            ->map(fn (array $item) => [
                'course' => $item['course'],
                'room' => $item['classroom'],
                'time' => substr((string) $item['start_time'], 0, 5).' - '.substr((string) $item['end_time'], 0, 5),
                'is_live' => $item['start_time'] <= $now && $now <= $item['end_time'],
                'date_label' => 'Hari ini',
            ])
            ->all();
    }

    /**
     * Tanggal yang diberi penanda di kalender mini.
     *
     * @return array<int, string>
     */
    private function classDates(int $studentId): array
    {
        return Attendance::query()
            ->select('date')
            ->where('student_id', $studentId)
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->distinct()
            ->pluck('date')
            ->map(fn ($date) => $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : (string) $date)
            ->all();
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
