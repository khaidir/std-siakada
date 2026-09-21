<?php

use App\Models\Assignment;
use App\Models\CourseOffering;
use App\Models\Lecturer;
use App\Models\Semester;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    // Buat dosen
    $this->dosen = User::factory()->create(['email' => 'dosen_tugas@test.com']);
    $this->dosen->assignRole('dosen');
    $this->dosen->givePermissionTo('assignment.manage');

    $this->lecturer = Lecturer::factory()->create(['user_id' => $this->dosen->id]);

    // Buat dosen lain (untuk test 403 lintas kelas)
    $this->dosenLain = User::factory()->create(['email' => 'dosen_lain_tugas@test.com']);
    $this->dosenLain->assignRole('dosen');
    $this->dosenLain->givePermissionTo('assignment.manage');
    $this->lecturerLain = Lecturer::factory()->create(['user_id' => $this->dosenLain->id]);

    // Buat semester aktif
    $this->semester = Semester::factory()->create(['is_active' => true]);

    // Buat kelas (course offering) untuk dosen
    $this->offering = CourseOffering::factory()->create([
        'lecturer_id' => $this->lecturer->id,
        'semester_id' => $this->semester->id,
    ]);

    // Buat kelas untuk dosen lain
    $this->offeringLain = CourseOffering::factory()->create([
        'lecturer_id' => $this->lecturerLain->id,
        'semester_id' => $this->semester->id,
    ]);
});

it('dosen dapat melihat halaman tugas', function () {
    $this->actingAs($this->dosen)
        ->get(route('dosen.tugas.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dosen/Tugas')
            ->has('offerings')
            ->has('assignments')
        );
});

it('dosen dapat membuat tugas baru', function () {
    $this->actingAs($this->dosen)
        ->post(route('dosen.tugas.store'), [
            'course_offering_id' => $this->offering->id,
            'title' => 'Tugas 1: Algoritma',
            'description' => 'Kerjakan soal algoritma',
            'due_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'max_score' => 100,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('assignments', [
        'course_offering_id' => $this->offering->id,
        'title' => 'Tugas 1: Algoritma',
        'max_score' => 100,
    ]);
});

it('dosen dapat mengupdate tugas', function () {
    $assignment = Assignment::factory()->create([
        'course_offering_id' => $this->offering->id,
        'title' => 'Tugas Lama',
        'max_score' => 100,
    ]);

    $this->actingAs($this->dosen)
        ->put(route('dosen.tugas.update', $assignment->id), [
            'course_offering_id' => $this->offering->id,
            'title' => 'Tugas Updated',
            'description' => 'Deskripsi baru',
            'due_date' => now()->addDays(14)->format('Y-m-d H:i:s'),
            'max_score' => 150,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('assignments', [
        'id' => $assignment->id,
        'title' => 'Tugas Updated',
        'max_score' => 150,
    ]);
});

it('dosen dapat menghapus tugas', function () {
    $assignment = Assignment::factory()->create([
        'course_offering_id' => $this->offering->id,
    ]);

    $this->actingAs($this->dosen)
        ->delete(route('dosen.tugas.destroy', $assignment->id))
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('assignments', ['id' => $assignment->id]);
});

it('dosen tidak bisa mengelola tugas di kelas yang bukan diampunya', function () {
    $this->actingAs($this->dosen)
        ->post(route('dosen.tugas.store'), [
            'course_offering_id' => $this->offeringLain->id,
            'title' => 'Tugas Curian',
            'max_score' => 100,
        ])
        ->assertForbidden();
});

it('dosen tidak bisa mengupdate tugas di kelas yang bukan diampunya', function () {
    $assignment = Assignment::factory()->create([
        'course_offering_id' => $this->offeringLain->id,
    ]);

    $this->actingAs($this->dosen)
        ->put(route('dosen.tugas.update', $assignment->id), [
            'course_offering_id' => $this->offeringLain->id,
            'title' => 'Update Curian',
            'max_score' => 100,
        ])
        ->assertForbidden();
});

it('dosen tidak bisa menghapus tugas di kelas yang bukan diampunya', function () {
    $assignment = Assignment::factory()->create([
        'course_offering_id' => $this->offeringLain->id,
    ]);

    $this->actingAs($this->dosen)
        ->delete(route('dosen.tugas.destroy', $assignment->id))
        ->assertForbidden();
});
