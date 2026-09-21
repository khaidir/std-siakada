<?php

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    $this->kaprodi = User::where('email', 'kaprodi@siakad.test')->firstOrFail();
    $this->dosen = User::where('email', 'dosen@siakad.test')->firstOrFail();
});

it('kaprodi dapat melihat halaman mata kuliah', function () {
    $this->actingAs($this->kaprodi)
        ->get(route('kaprodi.courses.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Kaprodi/Courses')
            ->has('courses'));
});

it('kaprodi dapat menambah mata kuliah di prodi-nya', function () {
    $this->actingAs($this->kaprodi)
        ->post(route('kaprodi.courses.store'), [
            'code' => 'IF999',
            'name' => 'Mata Kuliah Baru',
            'sks' => 3,
            'semester' => 5,
            'type' => 'wajib',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('courses', [
        'code' => 'IF999',
        'name' => 'Mata Kuliah Baru',
        'sks' => 3,
        'semester' => 5,
        'type' => 'wajib',
    ]);
});

it('kaprodi dapat mengupdate mata kuliah di prodi-nya', function () {
    $course = Course::where('code', 'IF101')->firstOrFail();

    $this->actingAs($this->kaprodi)
        ->put(route('kaprodi.courses.update', ['id' => $course->id]), [
            'code' => 'IF101',
            'name' => 'Algoritma Updated',
            'sks' => 4,
            'semester' => 1,
            'type' => 'wajib',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'name' => 'Algoritma Updated',
        'sks' => 4,
    ]);
});

it('kaprodi dapat menghapus mata kuliah di prodi-nya', function () {
    // Buat MK baru untuk dihapus.
    $course = Course::create([
        'study_program_id' => $this->kaprodi->lecturer->study_program_id,
        'code' => 'IF998',
        'name' => 'MK Akan Dihapus',
        'sks' => 2,
        'semester' => 3,
        'type' => 'pilihan',
    ]);

    $this->actingAs($this->kaprodi)
        ->delete(route('kaprodi.courses.destroy', ['id' => $course->id]))
        ->assertRedirect();

    $this->assertSoftDeleted('courses', ['id' => $course->id]);
});

it('kaprodi tidak bisa mengelola mata kuliah prodi lain', function () {
    // Cari MK dari prodi lain (bukan IF).
    $otherCourse = Course::where('code', 'TI101')->firstOrFail();

    $this->actingAs($this->kaprodi)
        ->put(route('kaprodi.courses.update', ['id' => $otherCourse->id]), [
            'code' => 'TI101',
            'name' => 'Changed Name',
            'sks' => 3,
            'semester' => 1,
            'type' => 'wajib',
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('course');
});

it('dosen tidak bisa mengakses halaman mata kuliah kaprodi', function () {
    $this->actingAs($this->dosen)
        ->get(route('kaprodi.courses.index'))
        ->assertForbidden();
});

it('kode mata kuliah harus unik', function () {
    $this->actingAs($this->kaprodi)
        ->post(route('kaprodi.courses.store'), [
            'code' => 'IF101', // Sudah ada di seeder.
            'name' => 'Duplikat',
            'sks' => 3,
            'semester' => 1,
            'type' => 'wajib',
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('code');
});
