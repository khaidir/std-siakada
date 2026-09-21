<?php

use App\Enums\DayOfWeek;
use App\Enums\GradeLetter;
use App\Enums\StudyPlanDetailStatus;
use App\Enums\StudyPlanStatus;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    $this->admin = User::where('email', 'admin@siakad.test')->firstOrFail();
    $this->kaprodi = User::where('email', 'kaprodi@siakad.test')->firstOrFail();
    $this->dosen = User::where('email', 'dosen@siakad.test')->firstOrFail();
    $this->mahasiswa = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();
    $this->pimpinan = User::where('email', 'pimpinan@siakad.test')->firstOrFail();

    $this->ifCourse = Course::where('code', 'IF101')->firstOrFail();
    $this->tkCourse = Course::where('code', 'TK101')->firstOrFail();
    $this->activeSemester = Semester::where('is_active', true)->firstOrFail();
    $this->classroom = Classroom::firstOrFail();

    // Helper membuat Grade deterministik tanpa rantai factory (hindari bentrok
    // unique code academic_years pada SQLite in-memory).
    $this->makeGrade = function (Student $student, CourseOffering $offering): Grade {
        $plan = StudyPlan::firstOrCreate(
            ['student_id' => $student->id, 'semester_id' => $offering->semester_id],
            ['status' => StudyPlanStatus::Approved],
        );

        $detail = StudyPlanDetail::firstOrCreate(
            ['study_plan_id' => $plan->id, 'course_offering_id' => $offering->id],
            ['status' => StudyPlanDetailStatus::Approved],
        );

        return Grade::firstOrCreate(
            ['study_plan_detail_id' => $detail->id],
            [
                'student_id' => $student->id,
                'course_offering_id' => $offering->id,
                'score' => 80,
                'letter_grade' => GradeLetter::A,
                'grade_point' => GradeLetter::A->point(),
            ],
        );
    };
});

it('super-admin melewati seluruh policy (Gate::before)', function () {
    expect($this->admin->can('delete', $this->ifCourse))->toBeTrue()
        ->and($this->admin->can('delete', $this->tkCourse))->toBeTrue()
        ->and($this->admin->can('update', $this->mahasiswa))->toBeTrue()
        ->and($this->admin->can('create', AcademicYear::class))->toBeTrue();
});

it('hanya super-admin yang kelola pengguna', function () {
    foreach ([$this->kaprodi, $this->dosen, $this->mahasiswa, $this->pimpinan] as $actor) {
        expect($actor->can('delete', $this->mahasiswa))->toBeFalse();
        expect($actor->can('update', $this->dosen))->toBeFalse();
        expect($actor->can('create', User::class))->toBeFalse();
    }
});

it('kaprodi hanya kelola mata kuliah di prodi-nya', function () {
    expect($this->kaprodi->can('update', $this->ifCourse))->toBeTrue()
        ->and($this->kaprodi->can('delete', $this->ifCourse))->toBeTrue()
        ->and($this->kaprodi->can('update', $this->tkCourse))->toBeFalse()
        ->and($this->kaprodi->can('delete', $this->tkCourse))->toBeFalse()
        ->and($this->dosen->can('update', $this->ifCourse))->toBeFalse()
        ->and($this->mahasiswa->can('update', $this->ifCourse))->toBeFalse()
        ->and($this->pimpinan->can('update', $this->ifCourse))->toBeFalse();

    // peran lain tetap bisa membaca
    expect($this->kaprodi->can('view', $this->ifCourse))->toBeTrue()
        ->and($this->dosen->can('view', $this->ifCourse))->toBeTrue()
        ->and($this->mahasiswa->can('view', $this->ifCourse))->toBeTrue();
});

it('hanya super-admin yang kelola penawaran kelas', function () {
    $offering = CourseOffering::firstOrFail();

    expect($this->admin->can('update', $offering))->toBeTrue()
        ->and($this->kaprodi->can('update', $offering))->toBeFalse()
        ->and($this->dosen->can('update', $offering))->toBeFalse();
});

it('mahasiswa hanya lihat nilai miliknya & dosen hanya nilai kelas diampu', function () {
    $ownGrade = $this->mahasiswa->student->grades()->firstOrFail();

    expect($this->mahasiswa->can('view', $ownGrade))->toBeTrue();

    $otherUser = User::factory()->create();
    $otherUser->assignRole('mahasiswa');
    $otherStudent = Student::factory()->create([
        'user_id' => $otherUser->id,
        'study_program_id' => StudyProgram::where('code', 'TK')->firstOrFail()->id,
    ]);
    $otherOffering = CourseOffering::create([
        'course_id' => $this->tkCourse->id,
        'semester_id' => $this->activeSemester->id,
        'lecturer_id' => $this->dosen->lecturer->id,
        'classroom_id' => $this->classroom->id,
        'day' => DayOfWeek::Senin->value,
        'start_time' => '13:00:00',
        'end_time' => '15:00:00',
        'quota' => 30,
    ]);
    $otherGrade = ($this->makeGrade)($otherStudent, $otherOffering);

    expect($this->mahasiswa->can('view', $otherGrade))->toBeFalse();

    // Nilai seeded berada di kelas dosen factory (bukan dosen login).
    expect($this->dosen->can('update', $ownGrade))->toBeFalse();

    // Kelas yang diampu dosen login -> boleh menilai.
    $dosenOffering = CourseOffering::create([
        'course_id' => $this->ifCourse->id,
        'semester_id' => $this->activeSemester->id,
        'lecturer_id' => $this->dosen->lecturer->id,
        'classroom_id' => $this->classroom->id,
        'day' => DayOfWeek::Selasa->value,
        'start_time' => '13:00:00',
        'end_time' => '15:00:00',
        'quota' => 30,
    ]);
    $dosenGrade = ($this->makeGrade)($this->mahasiswa->student, $dosenOffering);

    expect($this->dosen->can('update', $dosenGrade))->toBeTrue()
        ->and($this->dosen->can('view', $dosenGrade))->toBeTrue();
});

it('dosen pembimbing & mahasiswa kelola skripsi miliknya', function () {
    $thesis = $this->mahasiswa->student->thesis;

    expect($this->mahasiswa->can('view', $thesis))->toBeTrue()
        ->and($this->mahasiswa->can('update', $thesis))->toBeTrue()
        ->and($this->dosen->can('view', $thesis))->toBeTrue()
        ->and($this->dosen->can('update', $thesis))->toBeTrue()
        ->and($this->dosen->can('delete', $thesis))->toBeFalse()
        ->and($this->kaprodi->can('view', $thesis))->toBeTrue()
        ->and($this->kaprodi->can('update', $thesis))->toBeFalse()
        ->and($this->pimpinan->can('view', $thesis))->toBeTrue();
});

it('dosen PA approve KRS mahasiswa satu prodi & mahasiswa kelola KRS-nya', function () {
    $plan = $this->mahasiswa->student->studyPlans()->firstOrFail();

    expect($this->mahasiswa->can('view', $plan))->toBeTrue()
        ->and($this->mahasiswa->can('update', $plan))->toBeTrue()
        ->and($this->dosen->can('approve', $plan))->toBeTrue()
        ->and($this->kaprodi->can('approve', $plan))->toBeFalse();

    $otherUser = User::factory()->create();
    $otherUser->assignRole('mahasiswa');
    $otherStudent = Student::factory()->create([
        'user_id' => $otherUser->id,
        'study_program_id' => StudyProgram::where('code', 'TK')->firstOrFail()->id,
    ]);
    $otherPlan = StudyPlan::factory()->create([
        'student_id' => $otherStudent->id,
        'semester_id' => $this->activeSemester->id,
    ]);

    expect($this->dosen->can('approve', $otherPlan))->toBeFalse()
        ->and($this->mahasiswa->can('view', $otherPlan))->toBeFalse();
});

it('hanya super-admin yang kelola periode akademik', function () {
    $year = AcademicYear::firstOrFail();
    $semester = $this->activeSemester;

    expect($this->admin->can('create', AcademicYear::class))->toBeTrue()
        ->and($this->admin->can('update', $year))->toBeTrue()
        ->and($this->admin->can('update', $semester))->toBeTrue()
        ->and($this->kaprodi->can('create', AcademicYear::class))->toBeFalse()
        ->and($this->dosen->can('update', $year))->toBeFalse()
        ->and($this->dosen->can('update', $semester))->toBeFalse()
        ->and($this->mahasiswa->can('delete', $year))->toBeFalse()
        ->and($this->pimpinan->can('update', $year))->toBeFalse();

    // peran lain tetap read-only
    expect($this->mahasiswa->can('view', $year))->toBeTrue()
        ->and($this->pimpinan->can('view', $year))->toBeTrue()
        ->and($this->dosen->can('view', $semester))->toBeTrue();
});
