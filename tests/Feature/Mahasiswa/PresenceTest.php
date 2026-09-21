<?php

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Lecturer;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\User;
use App\Enums\DayOfWeek;
use App\Enums\SemesterType;
use App\Enums\StudyPlanStatus;
use App\Enums\StudyPlanDetailStatus;
use App\Enums\AttendanceStatus;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    // Buat data master
    $academicYear = AcademicYear::factory()->create(['code' => '2024/2025']);
    $semester = Semester::factory()->create([
        'academic_year_id' => $academicYear->id,
        'type' => SemesterType::Ganjil,
        'is_active' => true,
    ]);

    // Buat mahasiswa
    $user = User::factory()->create();
    $user->assignRole('mahasiswa');
    $student = Student::factory()->create(['user_id' => $user->id]);

    // Buat dosen + lecturer
    $lecturerUser = User::factory()->create();
    $lecturer = Lecturer::factory()->create(['user_id' => $lecturerUser->id]);

    // Buat mata kuliah
    $course = Course::factory()->create();

    // Buat ruangan
    $classroom = Classroom::factory()->create();

    // Buat course offering
    $offering = CourseOffering::factory()->create([
        'course_id' => $course->id,
        'semester_id' => $semester->id,
        'lecturer_id' => $lecturer->id,
        'classroom_id' => $classroom->id,
        'day' => DayOfWeek::Senin,
        'start_time' => '08:00:00',
        'end_time' => '09:40:00',
        'quota' => 30,
    ]);

    // Buat study plan + detail (approved)
    $studyPlan = StudyPlan::factory()->create([
        'student_id' => $student->id,
        'semester_id' => $semester->id,
        'status' => StudyPlanStatus::Approved,
    ]);

    StudyPlanDetail::factory()->create([
        'study_plan_id' => $studyPlan->id,
        'course_offering_id' => $offering->id,
        'status' => StudyPlanDetailStatus::Approved,
    ]);

    // Buat attendance records
    Attendance::factory()->count(5)->sequence(
        ['meeting_number' => 1, 'status' => AttendanceStatus::Hadir],
        ['meeting_number' => 2, 'status' => AttendanceStatus::Hadir],
        ['meeting_number' => 3, 'status' => AttendanceStatus::Izin],
        ['meeting_number' => 4, 'status' => AttendanceStatus::Hadir],
        ['meeting_number' => 5, 'status' => AttendanceStatus::Sakit],
    )->create([
        'course_offering_id' => $offering->id,
        'student_id' => $student->id,
    ]);

    $this->student = $student;
    $this->user = $user;
    $this->offering = $offering;
    $this->semester = $semester;
});

it('menampilkan halaman jadwal kuliah', function () {
    $this->actingAs($this->user)
        ->get(route('mahasiswa.jadwal.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Mahasiswa/Jadwal')
            ->has('schedules', 1)
            ->has('semesterLabel')
        );
});

it('menampilkan halaman presensi dengan offering terpilih', function () {
    $this->actingAs($this->user)
        ->get(route('mahasiswa.presensi.index', ['offering_id' => $this->offering->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Mahasiswa/Presensi')
            ->has('offerings')
            ->has('attendances', 5)
            ->has('percentage')
            ->where('selectedOfferingId', $this->offering->id)
        );
});

it('menampilkan halaman presensi tanpa offering_id (default pertama)', function () {
    $this->actingAs($this->user)
        ->get(route('mahasiswa.presensi.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Mahasiswa/Presensi')
            ->has('offerings')
            ->has('attendances')
            ->has('percentage')
        );
});

it('mengembalikan 403 jika bukan role mahasiswa', function () {
    $dosen = User::factory()->create();
    $dosen->assignRole('dosen');

    $this->actingAs($dosen)
        ->get(route('mahasiswa.jadwal.index'))
        ->assertForbidden();
});

it('menghitung persentase kehadiran dengan benar', function () {
    $this->actingAs($this->user)
        ->get(route('mahasiswa.presensi.index', ['offering_id' => $this->offering->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('percentage', 60)
        );
});
