<?php

use App\DTO\AttendanceData;
use App\DTO\BatchAttendanceData;
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
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

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

    $this->service = app(AttendanceService::class);
});

it('menyimpan presensi batch', function () {
    $this->service->store($this->offering->id, BatchAttendanceData::from([
        'attendances' => [
            AttendanceData::from([
                'course_offering_id' => $this->offering->id,
                'student_id' => $this->mahasiswa->student->id,
                'meeting_number' => 1,
                'date' => '2026-02-02',
                'status' => AttendanceStatus::Hadir->value,
            ]),
        ],
    ]));

    $attendance = Attendance::where('course_offering_id', $this->offering->id)
        ->where('student_id', $this->mahasiswa->student->id)
        ->where('meeting_number', 1)
        ->firstOrFail();

    expect($attendance->status)->toBe(AttendanceStatus::Hadir)
        ->and($attendance->date->toDateString())->toBe('2026-02-02');
});

it('menolak mahasiswa yang tidak terdaftar di kelas', function () {
    $otherStudent = Student::factory()->create([
        'study_program_id' => $this->course->study_program_id,
    ]);

    expect(fn () => $this->service->store($this->offering->id, BatchAttendanceData::from([
        'attendances' => [
            AttendanceData::from([
                'course_offering_id' => $this->offering->id,
                'student_id' => $otherStudent->id,
                'meeting_number' => 1,
                'date' => '2026-02-02',
                'status' => AttendanceStatus::Hadir->value,
            ]),
        ],
    ])))->toThrow(ValidationException::class);
});
