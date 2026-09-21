<?php

use App\Enums\DayOfWeek;
use App\Enums\StudyPlanDetailStatus;
use App\Enums\StudyPlanStatus;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Semester;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    $this->mahasiswa = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();
    $this->dosen = User::where('email', 'dosen@siakad.test')->firstOrFail();
    $this->semester = Semester::where('is_active', true)->firstOrFail();
    $this->course = Course::where('code', 'IF101')->firstOrFail();
    $this->classroom = Classroom::firstOrFail();

    // Buat offering.
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

    // Hapus KRS existing lalu buat baru dengan status approved.
    StudyPlan::where('student_id', $this->mahasiswa->student->id)
        ->where('semester_id', $this->semester->id)
        ->each(fn ($p) => $p->studyPlanDetails()->delete() && $p->delete());

    $plan = StudyPlan::create([
        'student_id' => $this->mahasiswa->student->id,
        'semester_id' => $this->semester->id,
        'status' => StudyPlanStatus::Approved,
    ]);

    StudyPlanDetail::create([
        'study_plan_id' => $plan->id,
        'course_offering_id' => $this->offering->id,
        'status' => StudyPlanDetailStatus::Approved,
    ]);

    // Buat tugas dengan due_date di masa depan.
    $this->assignment = Assignment::create([
        'course_offering_id' => $this->offering->id,
        'title' => 'Tugas 1',
        'description' => 'Kerjakan soal berikut.',
        'due_date' => now()->addDays(7),
        'max_score' => 100,
    ]);

    // Buat tugas dengan due_date sudah lewat.
    $this->pastAssignment = Assignment::create([
        'course_offering_id' => $this->offering->id,
        'title' => 'Tugas Lampau',
        'description' => 'Tugas yang sudah lewat.',
        'due_date' => now()->subDays(1),
        'max_score' => 100,
    ]);
});

it('mahasiswa dapat melihat halaman tugas', function () {
    $this->actingAs($this->mahasiswa)
        ->get(route('mahasiswa.tugas.index'))
        ->assertOk()
        ->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Mahasiswa/Tugas')
            ->has('assignments'));
});

it('mahasiswa dapat submit tugas sebelum tenggat', function () {
    $file = UploadedFile::fake()->create('tugas.pdf', 100);

    $this->actingAs($this->mahasiswa)
        ->post(route('mahasiswa.tugas.submit'), [
            'assignment_id' => $this->assignment->id,
            'file' => $file,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('assignment_submissions', [
        'assignment_id' => $this->assignment->id,
        'student_id' => $this->mahasiswa->student->id,
    ]);
});

it('mahasiswa tidak bisa submit tugas setelah tenggat', function () {
    $file = UploadedFile::fake()->create('tugas.pdf', 100);

    $this->actingAs($this->mahasiswa)
        ->post(route('mahasiswa.tugas.submit'), [
            'assignment_id' => $this->pastAssignment->id,
            'file' => $file,
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('assignment_id');
});

it('mahasiswa tidak bisa submit tugas kelas yang tidak diikuti', function () {
    // Buat offering lain yang tidak ada di KRS mahasiswa.
    $otherCourse = Course::where('code', 'IF102')->firstOrFail();
    $otherOffering = CourseOffering::create([
        'course_id' => $otherCourse->id,
        'semester_id' => $this->semester->id,
        'lecturer_id' => $this->dosen->lecturer->id,
        'classroom_id' => $this->classroom->id,
        'day' => DayOfWeek::Selasa->value,
        'start_time' => '09:00:00',
        'end_time' => '11:00:00',
        'quota' => 30,
    ]);

    $otherAssignment = Assignment::create([
        'course_offering_id' => $otherOffering->id,
        'title' => 'Tugas Kelas Lain',
        'description' => 'Tugas ini bukan untuk mahasiswa ini.',
        'due_date' => now()->addDays(7),
        'max_score' => 100,
    ]);

    $file = UploadedFile::fake()->create('tugas.pdf', 100);

    $this->actingAs($this->mahasiswa)
        ->post(route('mahasiswa.tugas.submit'), [
            'assignment_id' => $otherAssignment->id,
            'file' => $file,
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('assignment_id');
});
