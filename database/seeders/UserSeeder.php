<?php

namespace Database\Seeders;

use App\Enums\AcademicRank;
use App\Enums\AttendanceStatus;
use App\Enums\GradeLetter;
use App\Enums\InternshipLogApproval;
use App\Enums\InternshipStatus;
use App\Enums\StudentStatus;
use App\Enums\StudyPlanDetailStatus;
use App\Enums\StudyPlanStatus;
use App\Enums\ThesisStatus;
use App\Models\Attendance;
use App\Models\CourseOffering;
use App\Models\Grade;
use App\Models\Internship;
use App\Models\InternshipLog;
use App\Models\Lecturer;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\StudyProgram;
use App\Models\Thesis;
use App\Models\ThesisLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUser('Super Admin', 'admin@siakad.test', 'super-admin');

        $kaprodi = $this->seedUser('Kepala Program Studi', 'kaprodi@siakad.test', 'kaprodi');
        $this->seedLecturer($kaprodi, '0001112223', AcademicRank::LektorKepala, 'IF');
        StudyProgram::where('code', 'IF')->select(['id'])->first()?->update(['head_id' => $kaprodi->id]);

        $dosen = $this->seedUser('Dosen Pengampu', 'dosen@siakad.test', 'dosen');
        $dosenLecturer = $this->seedLecturer($dosen, '0001112224', AcademicRank::Lektor, 'IF');

        $mahasiswa = $this->seedUser('Mahasiswa', 'mahasiswa@siakad.test', 'mahasiswa');
        $student = $this->seedStudent($mahasiswa, '2025000001', 'IF');
        $this->seedStudentAcademicData($student, $dosen, $dosenLecturer);

        $this->seedUser('Pimpinan', 'pimpinan@siakad.test', 'pimpinan');
    }

    private function seedUser(string $name, string $email, string $role): User
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => 'password'],
        );

        $user->syncRoles([$role]);

        return $user;
    }

    private function seedLecturer(User $user, string $nidn, AcademicRank $rank, string $programCode): Lecturer
    {
        $program = StudyProgram::where('code', $programCode)->select(['id'])->first();

        return Lecturer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nidn' => $nidn,
                'study_program_id' => $program?->id,
                'academic_rank' => $rank,
            ],
        );
    }

    private function seedStudent(User $user, string $nim, string $programCode): Student
    {
        $program = StudyProgram::where('code', $programCode)->select(['id'])->first();

        return Student::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nim' => $nim,
                'study_program_id' => $program?->id,
                'entry_year' => '2025',
                'status' => StudentStatus::Aktif,
            ],
        );
    }

    private function seedStudentAcademicData(Student $student, User $approvedBy, Lecturer $supervisor): void
    {
        $semester = Semester::where('is_active', true)->select(['id'])->first();

        if (! $semester) {
            return;
        }

        $plan = StudyPlan::firstOrCreate(
            ['student_id' => $student->id, 'semester_id' => $semester->id],
            [
                'status' => StudyPlanStatus::Approved,
                'approved_by' => $approvedBy->id,
                'approved_at' => now(),
            ],
        );

        $offerings = CourseOffering::query()
            ->where('semester_id', $semester->id)
            ->whereHas('course', fn ($q) => $q->where('study_program_id', $student->study_program_id))
            ->select(['id', 'course_id', 'semester_id'])
            ->orderBy('id')
            ->limit(4)
            ->get();

        $scores = [90, 82, 75, 68];

        foreach ($offerings as $i => $offering) {
            $detail = StudyPlanDetail::firstOrCreate(
                ['study_plan_id' => $plan->id, 'course_offering_id' => $offering->id],
                ['status' => StudyPlanDetailStatus::Approved],
            );

            $score = $scores[$i % count($scores)];
            $letter = $this->letterFor($score);

            Grade::firstOrCreate(
                ['study_plan_detail_id' => $detail->id],
                [
                    'student_id' => $student->id,
                    'course_offering_id' => $offering->id,
                    'assignment_score' => $score,
                    'midterm_score' => $score - 5,
                    'final_score' => $score,
                    'score' => $score,
                    'letter_grade' => $letter,
                    'grade_point' => $letter->point(),
                ],
            );

            if ($i === 0) {
                Attendance::firstOrCreate(
                    ['course_offering_id' => $offering->id, 'student_id' => $student->id, 'meeting_number' => 1],
                    ['date' => '2025-09-01', 'status' => AttendanceStatus::Hadir],
                );

                Attendance::firstOrCreate(
                    ['course_offering_id' => $offering->id, 'student_id' => $student->id, 'meeting_number' => 2],
                    ['date' => '2025-09-08', 'status' => AttendanceStatus::Izin],
                );
            }
        }

        $this->seedThesis($student, $supervisor);
        $this->seedInternship($student, $supervisor);
    }

    private function seedThesis(Student $student, Lecturer $supervisor): void
    {
        $thesis = Thesis::firstOrCreate(
            ['student_id' => $student->id],
            [
                'title' => 'Analisis Performa Sistem Informasi Akademik Berbasis Web',
                'abstract' => 'Penelitian ini menganalisis performa sistem informasi akademik berbasis web menggunakan metrik responsivitas dan skalabilitas.',
                'supervisor_1_id' => $supervisor->id,
                'supervisor_2_id' => null,
                'status' => ThesisStatus::Proposal,
                'submission_date' => '2025-09-01',
            ],
        );

        ThesisLog::firstOrCreate(
            ['thesis_id' => $thesis->id, 'date' => '2025-09-05', 'activity' => 'Bimbingan Bab 1'],
            [
                'notes' => 'Revisi latar belakang dan rumusan masalah.',
                'supervisor_approval' => true,
            ],
        );
    }

    private function seedInternship(Student $student, Lecturer $supervisor): void
    {
        $internship = Internship::firstOrCreate(
            ['student_id' => $student->id],
            [
                'company_name' => 'PT Teknologi Nusantara',
                'address' => 'Jl. Sudirman No. 123, Jakarta',
                'supervisor_id' => $supervisor->id,
                'field_supervisor' => 'Budi Santoso',
                'start_date' => '2025-07-01',
                'end_date' => '2025-08-31',
                'status' => InternshipStatus::Berjalan,
            ],
        );

        InternshipLog::firstOrCreate(
            ['internship_id' => $internship->id, 'date' => '2025-07-02', 'activity' => 'Onboarding & pengenalan lingkungan kerja'],
            [
                'notes' => 'Mempelajari alur pengembangan perangkat lunak.',
                'approval' => InternshipLogApproval::Approved,
            ],
        );
    }

    private function letterFor(float $score): GradeLetter
    {
        return match (true) {
            $score >= 85 => GradeLetter::A,
            $score >= 80 => GradeLetter::AMinus,
            $score >= 75 => GradeLetter::BPlus,
            $score >= 70 => GradeLetter::B,
            $score >= 65 => GradeLetter::BMinus,
            $score >= 60 => GradeLetter::CPlus,
            $score >= 55 => GradeLetter::C,
            $score >= 50 => GradeLetter::D,
            default => GradeLetter::E,
        };
    }
}
