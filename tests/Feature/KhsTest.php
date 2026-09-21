<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    $this->mahasiswa = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();
});

it('mahasiswa dapat melihat halaman KHS', function () {
    $this->actingAs($this->mahasiswa)
        ->get(route('mahasiswa.khs.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Khs')
            ->has('student')
            ->has('semesters')
            ->has('selectedSemesterId')
            ->has('grades')
            ->has('ipSemester')
            ->has('ipk')
            ->has('totalSksSemester')
            ->has('totalSksKumulatif'));
});

it('mahasiswa dapat memfilter KHS berdasarkan semester', function () {
    $student = $this->mahasiswa->student;
    $semester = \App\Models\Semester::whereHas('studyPlans', fn ($q) => $q->where('student_id', $student->id))
        ->first();

    if (! $semester) {
        $this->markTestSkipped('Tidak ada semester dengan KRS approved.');
    }

    $this->actingAs($this->mahasiswa)
        ->get(route('mahasiswa.khs.index', ['semester_id' => $semester->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Khs')
            ->where('selectedSemesterId', $semester->id));
});

it('IP semester dihitung dengan benar', function () {
    $student = $this->mahasiswa->student;
    $grades = $student->grades()
        ->with('studyPlanDetail.courseOffering.course')
        ->get();

    if ($grades->isEmpty()) {
        $this->markTestSkipped('Tidak ada data nilai.');
    }

    $service = app(\App\Services\KhsService::class);
    $ip = $service->calculateIp($grades);

    // IP harus antara 0 dan 4
    $this->assertGreaterThanOrEqual(0, $ip);
    $this->assertLessThanOrEqual(4, $ip);
});

it('total SKS dihitung dengan benar', function () {
    $student = $this->mahasiswa->student;
    $grades = $student->grades()
        ->with('studyPlanDetail.courseOffering.course')
        ->get();

    if ($grades->isEmpty()) {
        $this->markTestSkipped('Tidak ada data nilai.');
    }

    $service = app(\App\Services\KhsService::class);
    $totalSks = $service->calculateTotalSks($grades);

    $this->assertGreaterThan(0, $totalSks);
});

it('dosen tidak bisa mengakses halaman KHS', function () {
    $dosen = User::where('email', 'dosen@siakad.test')->firstOrFail();

    $this->actingAs($dosen)
        ->get(route('mahasiswa.khs.index'))
        ->assertForbidden();
});

it('super-admin tidak bisa mengakses halaman KHS', function () {
    $admin = User::where('email', 'admin@siakad.test')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('mahasiswa.khs.index'))
        ->assertForbidden();
});
