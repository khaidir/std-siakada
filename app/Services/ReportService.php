<?php

namespace App\Services;

use App\Repositories\Contracts\DashboardRepository;
use App\Repositories\Contracts\ReportRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ReportService
{
    private const CACHE_TTL = 3600; // 1 jam

    public function __construct(
        private readonly DashboardRepository $dashboard,
        private readonly ReportRepository $reports,
    ) {}

    /**
     * Data dashboard pimpinan (agregat global).
     *
     * @return array<string, mixed>
     */
    public function dashboardData(): array
    {
        return Cache::remember('pimpinan_dashboard', self::CACHE_TTL, function () {
            $stats = $this->dashboard->pimpinanStats();

            return [
                'stats' => [
                    'total_students' => $stats['total_students'],
                    'average_gpa' => $stats['average_gpa'],
                    'total_lecturers' => $stats['total_lecturers'],
                    'total_programs' => $stats['total_programs'],
                ],
                'grade_distribution' => $stats['grade_distribution'],
                'gpa_trend' => $stats['gpa_trend'],
                'recent_activities' => $stats['recent_activities'] ?? [],
            ];
        });
    }

    /**
     * Data laporan kinerja dosen.
     *
     * @return array{workload: Collection, attendance: Collection}
     */
    public function lecturerPerformance(int $semesterId): array
    {
        return [
            'workload' => $this->reports->lecturerWorkload($semesterId),
            'attendance' => $this->reports->lecturerAttendance($semesterId),
        ];
    }

    /**
     * Data laporan KHS (kartu hasil studi).
     */
    public function studentGradeReports(?int $semesterId = null, ?int $studyProgramId = null): Collection
    {
        return $this->reports->studentGradeReports($semesterId, $studyProgramId);
    }

    /**
     * Data laporan presensi mahasiswa.
     */
    public function studentAttendance(?int $semesterId = null, ?int $studyProgramId = null): Collection
    {
        return $this->reports->studentAttendance($semesterId, $studyProgramId);
    }

    /**
     * Distribusi nilai.
     */
    public function gradeDistribution(?int $semesterId = null): Collection
    {
        return $this->reports->gradeDistribution($semesterId);
    }

    /**
     * Invalidasi cache dashboard.
     */
    public function invalidateCache(): void
    {
        Cache::forget('pimpinan_dashboard');
    }
}
