<?php

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseOffering;
use App\Models\Lecturer;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    // Buat dosen
    $this->dosen = User::factory()->create(['email' => 'dosen_nilai@test.com']);
    $this->dosen->assignRole('dosen');
    $this->dosen->givePermissionTo('assignment.manage');

    $this->lecturer = Lecturer::factory()->create(['user_id' => $this->dosen->id]);

    // Buat dosen lain
    $this->dosenLain = User::factory()->create(['email' => 'dosen_lain_nilai@test.com']);
    $this->dosenLain->assignRole('dosen');
    $this->dosenLain->givePermissionTo('assignment.manage');
    $this->lecturerLain = Lecturer::factory()->create(['user_id' => $this->dosenLain->id]);

    // Buat semester aktif
    $this->semester = Semester::factory()->create(['is_active' => true]);

    // Buat kelas
    $this->offering = CourseOffering::factory()->create([
        'lecturer_id' => $this->lecturer->id,
        'semester_id' => $this->semester->id,
    ]);

    $this->offeringLain = CourseOffering::factory()->create([
        'lecturer_id' => $this->lecturerLain->id,
        'semester_id' => $this->semester->id,
    ]);

    // Buat tugas
    $this->assignment = Assignment::factory()->create([
        'course_offering_id' => $this->offering->id,
        'title' => 'Tugas 1',
        'max_score' => 100,
    ]);

    $this->assignmentLain = Assignment::factory()->create([
        'course_offering_id' => $this->offeringLain->id,
        'title' => 'Tugas Lain',
        'max_score' => 100,
    ]);

    // Buat mahasiswa + submission
    $this->student = Student::factory()->create();
    $this->submission = AssignmentSubmission::factory()->create([
        'assignment_id' => $this->assignment->id,
        'student_id' => $this->student->id,
        'score' => null,
    ]);

    $this->submissionLain = AssignmentSubmission::factory()->create([
        'assignment_id' => $this->assignmentLain->id,
        'student_id' => $this->student->id,
        'score' => null,
    ]);
});

it('dosen dapat melihat halaman penilaian submission', function () {
    $this->actingAs($this->dosen)
        ->get(route('dosen.tugas.nilai.index', $this->assignment->id))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dosen/PenilaianTugas')
            ->has('assignment')
            ->has('submissions')
        );
});

it('dosen dapat menilai submission', function () {
    $this->actingAs($this->dosen)
        ->post(route('dosen.tugas.nilai.store', $this->assignment->id), [
            'grades' => [
                [
                    'submission_id' => $this->submission->id,
                    'score' => 85,
                    'feedback' => 'Bagus!',
                ],
            ],
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('assignment_submissions', [
        'id' => $this->submission->id,
        'score' => 85,
        'feedback' => 'Bagus!',
    ]);
});

it('skor submission tidak boleh melebihi max_score', function () {
    $this->actingAs($this->dosen)
        ->post(route('dosen.tugas.nilai.store', $this->assignment->id), [
            'grades' => [
                [
                    'submission_id' => $this->submission->id,
                    'score' => 150,
                    'feedback' => null,
                ],
            ],
        ])
        ->assertSessionHasErrors('score');
});

it('dosen tidak bisa menilai submission di kelas yang bukan diampunya', function () {
    $this->actingAs($this->dosen)
        ->post(route('dosen.tugas.nilai.store', $this->assignmentLain->id), [
            'grades' => [
                [
                    'submission_id' => $this->submissionLain->id,
                    'score' => 80,
                    'feedback' => null,
                ],
            ],
        ])
        ->assertForbidden();
});
