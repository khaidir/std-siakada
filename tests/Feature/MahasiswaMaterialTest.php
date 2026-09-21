<?php

use App\Enums\DayOfWeek;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseOffering;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    $this->mahasiswa = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();
    $this->dosen = User::where('email', 'dosen@siakad.test')->firstOrFail();
    $this->semester = Semester::where('is_active', true)->firstOrFail();
    $this->course = Course::where('code', 'IF101')->firstOrFail();

    // Ambil offering yang sudah ada dari seeder (mahasiswa sudah terdaftar di KRS approved).
    $this->offering = CourseOffering::where('course_id', $this->course->id)
        ->where('semester_id', $this->semester->id)
        ->firstOrFail();

    // Buat materi untuk offering tersebut.
    $this->material = CourseMaterial::create([
        'course_offering_id' => $this->offering->id,
        'title' => 'Materi Test',
        'description' => 'Deskripsi materi test',
        'uploaded_by' => $this->dosen->id,
    ]);
});

it('mahasiswa dapat melihat halaman materi', function () {
    $this->actingAs($this->mahasiswa)
        ->get(route('mahasiswa.materi.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Materi')
            ->has('offerings')
            ->has('materials')
            ->has('selected_offering_id'));
});

it('mahasiswa dapat melihat materi kelas yang diikuti', function () {
    $this->actingAs($this->mahasiswa)
        ->get(route('mahasiswa.materi.index', ['offering' => $this->offering->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Materi')
            ->where('selected_offering_id', $this->offering->id)
            ->has('materials', 1));
});

it('mahasiswa tidak bisa melihat materi kelas yang tidak diikuti', function () {
    // Buat offering baru yang tidak ada di KRS mahasiswa.
    $otherOffering = CourseOffering::create([
        'course_id' => $this->course->id,
        'semester_id' => $this->semester->id,
        'lecturer_id' => $this->dosen->lecturer->id,
        'classroom_id' => \App\Models\Classroom::firstOrFail()->id,
        'day' => DayOfWeek::Selasa->value,
        'start_time' => '08:00:00',
        'end_time' => '10:00:00',
        'quota' => 30,
    ]);

    CourseMaterial::create([
        'course_offering_id' => $otherOffering->id,
        'title' => 'Materi Rahasia',
        'uploaded_by' => $this->dosen->id,
    ]);

    $this->actingAs($this->mahasiswa)
        ->get(route('mahasiswa.materi.index', ['offering' => $otherOffering->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Materi')
            ->has('materials', 0));
});
