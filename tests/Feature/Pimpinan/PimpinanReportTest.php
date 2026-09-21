<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Grade;
use App\Models\CourseOffering;
use App\Models\Semester;
use App\Models\AcademicYear;
use App\Models\StudyProgram;
use App\Models\Course;
use App\Models\LecturerAttendance;
use App\Models\Attendance;
use App\Enums\GradeLetter;
use App\Enums\LecturerAttendanceStatus;
use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    // Buat user pimpinan untuk testing
    $this->pimpinan = User::factory()->create([
        'name' => 'Pimpinan',
        'email' => 'pimpinan_test@siakad.test',
    ]);
    $this->pimpinan->assignRole('pimpinan');
    $this->pimpinan->givePermissionTo('report.view', 'dashboard.view');
});

// --- Dashboard ---

it('pimpinan dapat melihat dashboard eksekutif', function () {
    $this->actingAs($this->pimpinan)
        ->get(route('pimpinan.dashboard'))
        ->assertOk();
});

it('dashboard menampilkan statistik', function () {
    $response = $this->actingAs($this->pimpinan)
        ->get(route('pimpinan.dashboard'))
        ->assertOk();

    $response->assertInertia(fn ($page) => $page
        ->component('Pimpinan/Dashboard')
        ->has('stats.total_students')
        ->has('stats.average_gpa')
        ->has('stats.total_lecturers')
        ->has('stats.total_programs')
    );
});

// --- Laporan ---

it('pimpinan dapat melihat halaman laporan', function () {
    $this->actingAs($this->pimpinan)
        ->get(route('pimpinan.laporan'))
        ->assertOk();
});

it('laporan KHS dapat diakses dengan filter semester', function () {
    $semester = Semester::first();

    if (! $semester) {
        $this->markTestSkipped('Tidak ada data semester.');
    }

    $this->actingAs($this->pimpinan)
        ->get(route('pimpinan.laporan', ['tab' => 'khs', 'semester_id' => $semester->id]))
        ->assertOk();
});

it('laporan presensi mahasiswa dapat diakses', function () {
    $semester = Semester::first();

    if (! $semester) {
        $this->markTestSkipped('Tidak ada data semester.');
    }

    $this->actingAs($this->pimpinan)
        ->get(route('pimpinan.laporan', ['tab' => 'presensi', 'semester_id' => $semester->id]))
        ->assertOk();
});

it('laporan kinerja dosen menampilkan beban mengajar dan kehadiran', function () {
    $semester = Semester::first();

    if (! $semester) {
        $this->markTestSkipped('Tidak ada data semester.');
    }

    $response = $this->actingAs($this->pimpinan)
        ->get(route('pimpinan.laporan', ['tab' => 'kinerja_dosen', 'semester_id' => $semester->id]))
        ->assertOk();

    $response->assertInertia(fn ($page) => $page
        ->component('pimpinan/Laporan')
        ->has('lecturer_performance.workload')
        ->has('lecturer_performance.attendance')
    );
});

// --- Role Access ---

it('dosen tidak dapat mengakses dashboard pimpinan', function () {
    $user = User::where('email', 'dosen@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('pimpinan.dashboard'))
        ->assertForbidden();
});

it('mahasiswa tidak dapat mengakses laporan pimpinan', function () {
    $user = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('pimpinan.laporan'))
        ->assertForbidden();
});

// --- Endpoint Mutasi Tidak Ada ---

it('tidak ada endpoint POST untuk dashboard pimpinan', function () {
    $this->actingAs($this->pimpinan)
        ->post(route('pimpinan.dashboard'))
        ->assertStatus(405);
});

it('tidak ada endpoint POST untuk laporan pimpinan', function () {
    $this->actingAs($this->pimpinan)
        ->post(route('pimpinan.laporan'))
        ->assertStatus(405);
});
