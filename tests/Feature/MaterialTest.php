<?php

use App\Enums\DayOfWeek;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseOffering;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    $this->dosen = User::where('email', 'dosen@siakad.test')->firstOrFail();
    $this->kaprodi = User::where('email', 'kaprodi@siakad.test')->firstOrFail();

    $this->course = Course::where('code', 'IF101')->firstOrFail();
    $this->semester = Semester::where('is_active', true)->firstOrFail();

    $this->offering = CourseOffering::create([
        'course_id' => $this->course->id,
        'semester_id' => $this->semester->id,
        'lecturer_id' => $this->dosen->lecturer->id,
        'classroom_id' => \App\Models\Classroom::firstOrFail()->id,
        'day' => DayOfWeek::Senin->value,
        'start_time' => '13:00:00',
        'end_time' => '15:00:00',
        'quota' => 30,
    ]);
});

it('dosen dapat melihat halaman materi', function () {
    $this->actingAs($this->dosen)
        ->get(route('dosen.materi.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dosen/Materi')
            ->has('offerings')
            ->has('materials')
            ->has('selected_offering_id'));
});

it('dosen dapat menambah materi tanpa file', function () {
    $this->actingAs($this->dosen)
        ->post(route('dosen.materi.store'), [
            'course_offering_id' => $this->offering->id,
            'title' => 'Materi Pertemuan 1',
            'description' => 'Pengantar kuliah',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('course_materials', [
        'course_offering_id' => $this->offering->id,
        'title' => 'Materi Pertemuan 1',
        'description' => 'Pengantar kuliah',
    ]);
});

it('dosen dapat menambah materi dengan file', function () {
    $file = UploadedFile::fake()->create('materi.pdf', 100);

    $this->actingAs($this->dosen)
        ->post(route('dosen.materi.store'), [
            'course_offering_id' => $this->offering->id,
            'title' => 'Materi dengan File',
            'description' => 'File PDF materi',
            'file' => $file,
        ])
        ->assertRedirect();

    $material = CourseMaterial::where('course_offering_id', $this->offering->id)
        ->where('title', 'Materi dengan File')
        ->firstOrFail();

    expect($material->file_path)->not->toBeNull();
    expect($material->uploaded_by)->toBe($this->dosen->id);
});

it('dosen dapat mengedit materi', function () {
    $material = CourseMaterial::create([
        'course_offering_id' => $this->offering->id,
        'title' => 'Materi Lama',
        'description' => 'Deskripsi lama',
        'uploaded_by' => $this->dosen->id,
    ]);

    $this->actingAs($this->dosen)
        ->put(route('dosen.materi.update', ['id' => $material->id]), [
            'course_offering_id' => $this->offering->id,
            'title' => 'Materi Baru',
            'description' => 'Deskripsi baru',
        ])
        ->assertRedirect();

    $material->refresh();

    expect($material->title)->toBe('Materi Baru')
        ->and($material->description)->toBe('Deskripsi baru');
});

it('dosen dapat menghapus materi', function () {
    $material = CourseMaterial::create([
        'course_offering_id' => $this->offering->id,
        'title' => 'Materi Akan Dihapus',
        'uploaded_by' => $this->dosen->id,
    ]);

    $this->actingAs($this->dosen)
        ->delete(route('dosen.materi.destroy', ['id' => $material->id]))
        ->assertRedirect();

    $this->assertDatabaseMissing('course_materials', ['id' => $material->id]);
});

it('menolak dosen mengelola materi lintas kelas (403)', function () {
    $otherOffering = CourseOffering::create([
        'course_id' => $this->course->id,
        'semester_id' => $this->semester->id,
        'lecturer_id' => $this->kaprodi->lecturer->id,
        'classroom_id' => \App\Models\Classroom::firstOrFail()->id,
        'day' => DayOfWeek::Selasa->value,
        'start_time' => '08:00:00',
        'end_time' => '10:00:00',
        'quota' => 30,
    ]);

    $this->actingAs($this->dosen)
        ->post(route('dosen.materi.store'), [
            'course_offering_id' => $otherOffering->id,
            'title' => 'Materi Curian',
        ])
        ->assertForbidden();
});

it('validasi file materi', function () {
    $file = UploadedFile::fake()->create('virus.exe', 100);

    $this->actingAs($this->dosen)
        ->post(route('dosen.materi.store'), [
            'course_offering_id' => $this->offering->id,
            'title' => 'Materi Berbahaya',
            'file' => $file,
        ])
        ->assertSessionHasErrors('file');
});
