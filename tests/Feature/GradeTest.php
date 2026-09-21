<?php

use App\Enums\DayOfWeek;
use App\Enums\GradeLetter;
use App\Enums\StudyPlanDetailStatus;
use App\Enums\StudyPlanStatus;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    $this->detail = StudyPlanDetail::firstOrCreate(
        ['study_plan_id' => $plan->id, 'course_offering_id' => $this->offering->id],
        ['status' => StudyPlanDetailStatus::Approved],
    );
});

it('dosen menyimpan nilai batch sukses', function () {
    $this->actingAs($this->dosen)
        ->post(route('dosen.nilai.store'), [
            'course_offering_id' => $this->offering->id,
            'grades' => [
                [
                    'study_plan_detail_id' => $this->detail->id,
                    'student_id' => $this->mahasiswa->student->id,
                    'assignment_score' => 80,
                    'midterm_score' => 70,
                    'final_score' => 90,
                    'score' => null,
                ],
            ],
        ])
        ->assertRedirect();

    $grade = Grade::where('study_plan_detail_id', $this->detail->id)->firstOrFail();

    expect((float) $grade->score)->toBe(82.0)
        ->and($grade->letter_grade)->toBe(GradeLetter::AMinus)
        ->and((float) $grade->grade_point)->toBe(3.75);
});

it('merecalculate IPK mahasiswa setelah simpan nilai', function () {
    $student = Student::factory()->create([
        'study_program_id' => $this->course->study_program_id,
        'gpa' => 0,
        'total_sks' => 0,
    ]);

    $plan = StudyPlan::firstOrCreate(
        ['student_id' => $student->id, 'semester_id' => $this->semester->id],
        ['status' => StudyPlanStatus::Approved],
    );

    $detail = StudyPlanDetail::firstOrCreate(
        ['study_plan_id' => $plan->id, 'course_offering_id' => $this->offering->id],
        ['status' => StudyPlanDetailStatus::Approved],
    );

    $this->actingAs($this->dosen)
        ->post(route('dosen.nilai.store'), [
            'course_offering_id' => $this->offering->id,
            'grades' => [
                [
                    'study_plan_detail_id' => $detail->id,
                    'student_id' => $student->id,
                    'assignment_score' => null,
                    'midterm_score' => null,
                    'final_score' => null,
                    'score' => 90,
                ],
            ],
        ])
        ->assertRedirect();

    $student->refresh();

    expect((float) $student->gpa)->toBe(4.0)
        ->and((int) $student->total_sks)->toBe(3);
});

it('menolak dosen memberi nilai lintas kelas (403)', function () {
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
        ->post(route('dosen.nilai.store'), [
            'course_offering_id' => $otherOffering->id,
            'grades' => [
                [
                    'study_plan_detail_id' => $this->detail->id,
                    'student_id' => $this->mahasiswa->student->id,
                    'score' => 80,
                ],
            ],
        ])
        ->assertForbidden();
});

it('menolak skor komponen di luar rentang (invalid)', function () {
    $this->actingAs($this->dosen)
        ->post(route('dosen.nilai.store'), [
            'course_offering_id' => $this->offering->id,
            'grades' => [
                [
                    'study_plan_detail_id' => $this->detail->id,
                    'student_id' => $this->mahasiswa->student->id,
                    'assignment_score' => 150,
                    'midterm_score' => 70,
                    'final_score' => 90,
                    'score' => null,
                ],
            ],
        ])
        ->assertSessionHasErrors('grades.0.assignment_score');
});
