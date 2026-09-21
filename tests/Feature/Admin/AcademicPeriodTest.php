<?php

use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

// --- Tahun Ajaran ---

it('super-admin dapat melihat halaman periode akademik', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.periods.index'))
        ->assertOk();
});

it('super-admin dapat membuat tahun ajaran baru', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->post(route('admin.periods.academic-year.store'), [
            'code' => '2025/2026',
            'name' => 'Tahun Akademik 2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('academic_years', [
        'code' => '2025/2026',
        'name' => 'Tahun Akademik 2025/2026',
    ]);
});

it('super-admin dapat mengupdate tahun ajaran', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $ay = AcademicYear::firstOrFail();

    $this->actingAs($user)
        ->put(route('admin.periods.academic-year.update', $ay->id), [
            'code' => '2025/2026',
            'name' => 'Tahun Akademik 2025/2026 (Updated)',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('academic_years', [
        'id' => $ay->id,
        'name' => 'Tahun Akademik 2025/2026 (Updated)',
    ]);
});

it('super-admin dapat menghapus tahun ajaran', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $ay = AcademicYear::firstOrFail();

    $this->actingAs($user)
        ->delete(route('admin.periods.academic-year.destroy', $ay->id))
        ->assertRedirect();

    $this->assertDatabaseMissing('academic_years', ['id' => $ay->id]);
});

it('set aktif tahun ajaran menonaktifkan yang lain', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();

    // Buat dua tahun ajaran
    $ay1 = AcademicYear::factory()->create(['code' => '2024/2025', 'is_active' => true]);
    $ay2 = AcademicYear::factory()->create(['code' => '2025/2026', 'is_active' => false]);

    $this->actingAs($user)
        ->put(route('admin.periods.academic-year.update', $ay2->id), [
            'code' => '2025/2026',
            'name' => $ay2->name,
            'start_date' => $ay2->start_date->format('Y-m-d'),
            'end_date' => $ay2->end_date->format('Y-m-d'),
            'is_active' => true,
        ])
        ->assertRedirect();

    $this->assertFalse(AcademicYear::find($ay1->id)->is_active);
    $this->assertTrue(AcademicYear::find($ay2->id)->is_active);
});

it('tahun ajaran dengan tanggal invalid ditolak', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->post(route('admin.periods.academic-year.store'), [
            'code' => '2025/2026',
            'name' => 'Tahun Akademik 2025/2026',
            'start_date' => '2026-06-30',
            'end_date' => '2025-07-01',
            'is_active' => false,
        ])
        ->assertSessionHasErrors('end_date');
});

// --- Semester ---

it('super-admin dapat membuat semester baru', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $ay = AcademicYear::firstOrFail();

    $this->actingAs($user)
        ->post(route('admin.periods.semester.store'), [
            'academic_year_id' => $ay->id,
            'type' => 'ganjil',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'is_active' => false,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('semesters', [
        'academic_year_id' => $ay->id,
        'type' => 'ganjil',
    ]);
});

it('super-admin dapat mengupdate semester', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $semester = Semester::firstOrFail();

    $this->actingAs($user)
        ->put(route('admin.periods.semester.update', $semester->id), [
            'academic_year_id' => $semester->academic_year_id,
            'type' => 'genap',
            'start_date' => $semester->start_date->format('Y-m-d'),
            'end_date' => $semester->end_date->format('Y-m-d'),
            'is_active' => false,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('semesters', [
        'id' => $semester->id,
        'type' => 'genap',
    ]);
});

it('super-admin dapat menghapus semester', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $semester = Semester::firstOrFail();

    $this->actingAs($user)
        ->delete(route('admin.periods.semester.destroy', $semester->id))
        ->assertRedirect();

    $this->assertDatabaseMissing('semesters', ['id' => $semester->id]);
});

it('set aktif semester menonaktifkan yang lain', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $ay = AcademicYear::firstOrFail();

    // Buat dua semester
    $sem1 = Semester::factory()->create([
        'academic_year_id' => $ay->id,
        'type' => 'ganjil',
        'is_active' => true,
    ]);
    $sem2 = Semester::factory()->create([
        'academic_year_id' => $ay->id,
        'type' => 'genap',
        'is_active' => false,
    ]);

    $this->actingAs($user)
        ->put(route('admin.periods.semester.update', $sem2->id), [
            'academic_year_id' => $ay->id,
            'type' => 'genap',
            'start_date' => $sem2->start_date->format('Y-m-d'),
            'end_date' => $sem2->end_date->format('Y-m-d'),
            'is_active' => true,
        ])
        ->assertRedirect();

    $this->assertFalse(Semester::find($sem1->id)->is_active);
    $this->assertTrue(Semester::find($sem2->id)->is_active);
});

it('semester dengan tanggal invalid ditolak', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $ay = AcademicYear::firstOrFail();

    $this->actingAs($user)
        ->post(route('admin.periods.semester.store'), [
            'academic_year_id' => $ay->id,
            'type' => 'ganjil',
            'start_date' => '2025-12-31',
            'end_date' => '2025-07-01',
            'is_active' => false,
        ])
        ->assertSessionHasErrors('end_date');
});

// --- Role Access ---

it('dosen tidak dapat membuat tahun ajaran', function () {
    $user = User::where('email', 'dosen@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->post(route('admin.periods.academic-year.store'), [
            'code' => '2025/2026',
            'name' => 'Test',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
        ])
        ->assertForbidden();
});

it('mahasiswa tidak dapat mengakses halaman periode akademik', function () {
    $user = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.periods.index'))
        ->assertForbidden();
});
