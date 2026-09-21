<?php

use App\Enums\AttendanceStatus;
use App\Enums\DayOfWeek;
use App\Enums\StudyPlanDetailStatus;
use App\Enums\StudyPlanStatus;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    $this->dosen = User::where('email', 'dosen@siakad.test')->firstOrFail();
    $this->mahasiswa = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();

    $this->course = Course::where('code', 'IF101')->firstOrFail();
    $this->semester = Semester::where('is_active', true)->firstOrFail();
    $this->classroom = Classroom::firstOrFail();

    $this->offering = CourseOffering::create([
        'course_id' => $this->course->id,
        'semester_id' => $this->semester->id,
        'lecturer_id' => $this->dosen->lecturer->id,
        'classroom_id' => $this->classroom->id,
        'day' => DayOfWeek::Senin->value,
        'start_time' => '13:00:00',
        'end_time' => '15:00:00',
        'quota' => 30,
    ]);

    $plan = StudyPlan::firstOrCreate(
        ['student_id' => $this->mahasiswa->student->id, 'semester_id' => $this->semester->id],
        ['status' => StudyPlanStatus::Approved],
    );

    StudyPlanDetail::firstOrCreate(
        ['study_plan_id' => $plan->id, 'course_offering_id' => $this->offering->id],
        ['status' => StudyPlanDetailStatus::Approved],
    );
});

it('dosen menyimpan presensi batch sukses', function () {
    $this->actingAs($this->dosen)
        ->post(route('dosen.presensi.store'), [
            'course_offering_id' => $this->offering->id,
            'meeting_number' => 1,
            'date' => '2026-02-02',
            'attendances' => [
                [
                    'student_id' => $this->mahasiswa->student->id,
                    'status' => 'hadir',
                ],
            ],
        ])
        ->assertRedirect();

    $attendance = Attendance::where('course_offering_id', $this->offering->id)
        ->where('student_id', $this->mahasiswa->student->id)
        ->where('meeting_number', 1)
        ->firstOrFail();

    expect($attendance->status)->toBe(AttendanceStatus::Hadir)
        ->and($attendance->date->toDateString())->toBe('2026-02-02');
});

it('menolak dosen presensi lintas kelas (403)', function () {
    $kaprodi = User::where('email', 'kaprodi@siakad.test')->firstOrFail();

    $otherOffering = CourseOffering::create([
        'course_id' => $this->course->id,
        'semester_id' => $this->semester->id,
        'lecturer_id' => $kaprodi->lecturer->id,
        'classroom_id' => $this->classroom->id,
        'day' => DayOfWeek::Selasa->value,
        'start_time' => '08:00:00',
        'end_time' => '10:00:00',
        'quota' => 30,
    ]);

    $this->actingAs($this->dosen)
        ->post(route('dosen.presensi.store'), [
            'course_offering_id' => $otherOffering->id,
            'meeting_number' => 1,
            'date' => '2026-02-02',
            'attendances' => [
                [
                    'student_id' => $this->mahasiswa->student->id,
                    'status' => 'hadir',
                ],
            ],
        ])
        ->assertForbidden();
});

it('menghasilkan rekap presensi yang benar', function () {
    // Mahasiswa kedua terdaftar pada kelas yang sama.
    $otherStudent = Student::factory()->create([
        'study_program_id' => $this->course->study_program_id,
    ]);

    $plan = StudyPlan::firstOrCreate(
        ['student_id' => $otherStudent->id, 'semester_id' => $this->semester->id],
        ['status' => StudyPlanStatus::Approved],
    );

    StudyPlanDetail::firstOrCreate(
        ['study_plan_id' => $plan->id, 'course_offering_id' => $this->offering->id],
        ['status' => StudyPlanDetailStatus::Approved],
    );

    $this->actingAs($this->dosen)
        ->post(route('dosen.presensi.store'), [
            'course_offering_id' => $this->offering->id,
            'meeting_number' => 1,
            'date' => '2026-02-02',
            'attendances' => [
                ['student_id' => $this->mahasiswa->student->id, 'status' => 'hadir'],
                ['student_id' => $otherStudent->id, 'status' => 'izin'],
            ],
        ])
        ->assertRedirect();

    $this->actingAs($this->dosen)
        ->get(route('dosen.presensi.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dosen/Presensi')
            ->has('recap', 1)
            ->where('recap.0.meeting_number', 1)
            ->where('recap.0.date', '2026-02-02')
            ->where('recap.0.hadir', 1)
            ->where('recap.0.izin', 1)
            ->where('recap.0.sakit', 0)
            ->where('recap.0.alpha', 0)
            ->where('recap.0.total', 2));
});
