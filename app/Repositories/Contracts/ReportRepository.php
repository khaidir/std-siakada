<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ReportRepository
{
    /**
     * Statistik mahasiswa: total dan IPK rata-rata.
     *
     * @return array{total: int, avg_gpa: float}
     */
    public function studentStats(): array;

    /**
     * Distribusi nilai huruf.
     *
     * @return Collection<int, array{letter: string, total: int}>
     */
    public function gradeDistribution(?int $semesterId = null): Collection;

    /**
     * Beban mengajar dosen: jumlah kelas & total SKS per dosen.
     *
     * @return Collection<int, array{lecturer_id: int, lecturer_name: string, nidn: string, total_classes: int, total_sks: int}>
     */
    public function lecturerWorkload(int $semesterId): Collection;

    /**
     * Persentase kehadiran mengajar per dosen.
     *
     * @return Collection<int, array{lecturer_id: int, lecturer_name: string, nidn: string, total_sessions: int, attended_sessions: int, attendance_percentage: float}>
     */
    public function lecturerAttendance(int $semesterId): Collection;

    /**
     * Data KHS (kartu hasil studi) per mahasiswa per semester.
     *
     * @return Collection<int, array{student_id: int, nim: string, student_name: string, study_program: string, semester: string, total_sks: int, gpa: float}>
     */
    public function studentGradeReports(?int $semesterId = null, ?int $studyProgramId = null): Collection;

    /**
     * Data presensi mahasiswa per semester.
     *
     * @return Collection<int, array{student_id: int, nim: string, student_name: string, total_sessions: int, attended: int, percentage: float}>
     */
    public function studentAttendance(?int $semesterId = null, ?int $studyProgramId = null): Collection;
}
