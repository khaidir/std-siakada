<?php

use App\Enums\AttendanceStatus;
use App\Enums\DayOfWeek;
use App\Enums\SemesterType;
use App\Enums\StudyPlanStatus;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Grade;
use App\Models\Lecturer;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\User;
use App\Repositories\Contracts\DashboardRepository;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $year = AcademicYear::factory()->create();
    $semester = Semester::factory()->create([
        'academic_year_id' => $year->id,
        'type' => SemesterType::Ganjil,
        'is_active' => true,
    ]);

    $this->user = User::factory()->create();
    $this->user->assignRole('mahasiswa');
    $this->student = Student::factory()->create(['user_id' => $this->user->id]);

    $lecturer = Lecturer::factory()->create(['user_id' => User::factory()->create()->id]);
    $course = Course::factory()->create(['name' => 'Struktur Data', 'sks' => 3]);

    $this->offering = CourseOffering::factory()->create([
        'course_id' => $course->id,
        'semester_id' => $semester->id,
        'lecturer_id' => $lecturer->id,
        'classroom_id' => Classroom::factory()->create()->id,
        'day' => DayOfWeek::Senin,
        'start_time' => '08:00:00',
        'end_time' => '09:40:00',
        'quota' => 30,
    ]);

    $plan = StudyPlan::factory()->create([
        'student_id' => $this->student->id,
        'semester_id' => $semester->id,
        'status' => StudyPlanStatus::Approved,
    ]);

    $this->detail = StudyPlanDetail::factory()->create([
        'study_plan_id' => $plan->id,
        'course_offering_id' => $this->offering->id,
    ]);

    $this->repo = app(DashboardRepository::class);
});

it('menghitung sebaran huruf mutu untuk donut KHS', function () {
    Grade::factory()->create([
        'study_plan_detail_id' => $this->detail->id,
        'student_id' => $this->student->id,
        'course_offering_id' => $this->offering->id,
        'score' => 88,
        'letter_grade' => 'A',
        'grade_point' => 4.00,
    ]);

    $stats = $this->repo->mahasiswaStats($this->student->id);

    expect($stats['khs'])->toBe([['label' => 'A', 'value' => 1]]);
});

it('menghitung persentase kehadiran per mata kuliah', function () {
    // 3 hadir dari 4 pertemuan = 75%
    foreach ([AttendanceStatus::Hadir, AttendanceStatus::Hadir, AttendanceStatus::Hadir, AttendanceStatus::Alpha] as $i => $status) {
        Attendance::factory()->create([
            'course_offering_id' => $this->offering->id,
            'student_id' => $this->student->id,
            'meeting_number' => $i + 1,
            'date' => now()->subDays(4 - $i)->toDateString(),
            'status' => $status,
        ]);
    }

    $stats = $this->repo->mahasiswaStats($this->student->id);

    expect($stats['attendance'])->toHaveCount(1)
        ->and($stats['attendance'][0]['course'])->toBe('Struktur Data')
        ->and($stats['attendance'][0]['percentage'])->toBe(75)
        ->and($stats['attendance'][0]['sublabel'])->toBe('3 dari 4 pertemuan');
});

it('menandai tanggal kelas bulan ini di kalender', function () {
    Attendance::factory()->create([
        'course_offering_id' => $this->offering->id,
        'student_id' => $this->student->id,
        'meeting_number' => 1,
        'date' => now()->startOfMonth()->addDay()->toDateString(),
        'status' => AttendanceStatus::Hadir,
    ]);

    $stats = $this->repo->mahasiswaStats($this->student->id);

    expect($stats['calendar_marks'])->toContain(now()->startOfMonth()->addDay()->format('Y-m-d'));
});

it('mengambil pengumuman untuk mahasiswa dan mengabaikan yang belum terbit', function () {
    Announcement::factory()->create([
        'title' => 'Belum terbit',
        'content' => 'Draf',
        'target_role' => 'mahasiswa',
        'published_at' => null,
    ]);
    Announcement::factory()->create([
        'title' => 'Jadwal UTS',
        'content' => 'UTS dimulai 10 Oktober.',
        'target_role' => 'mahasiswa',
        'published_at' => now()->subHour(),
    ]);

    $stats = $this->repo->mahasiswaStats($this->student->id);

    expect($stats['announcement']['title'])->toBe('Jadwal UTS');
});

it('mengembalikan bagian kosong dengan aman saat mahasiswa belum punya data', function () {
    $stats = $this->repo->mahasiswaStats($this->student->id);

    expect($stats['khs'])->toBe([])
        ->and($stats['attendance'])->toBe([])
        ->and($stats['grades'])->toBe([])
        ->and($stats['thesis'])->toBeNull()
        ->and($stats['announcement'])->toBeNull();
});
