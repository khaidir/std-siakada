<?php

use App\Enums\LecturerAttendanceStatus;
use App\Models\CourseOffering;
use App\Models\Lecturer;
use App\Models\LecturerAttendance;
use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed('RoleAndPermissionSeeder');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('dosen dapat melihat halaman kehadiran', function () {
    $user = User::factory()->create()->assignRole('dosen');
    $lecturer = Lecturer::factory()->create(['user_id' => $user->id]);
    $user->refresh();

    $this->actingAs($user)
        ->get(route('dosen.kehadiran.index'))
        ->assertOk();
});

it('dosen dapat check-in ke kelas yang diampu', function () {
    $user = User::factory()->create()->assignRole('dosen');
    $lecturer = Lecturer::factory()->create(['user_id' => $user->id]);
    $course = Course::factory()->create();
    $offering = CourseOffering::factory()->create([
        'lecturer_id' => $lecturer->id,
        'course_id' => $course->id,
        'start_time' => '07:00:00',
    ]);

    $this->actingAs($user)
        ->post(route('dosen.kehadiran.check-in'), [
            'course_offering_id' => $offering->id,
            'date' => now()->toDateString(),
            'check_in' => now()->format('H:i:s'),
        ])
        ->assertSessionHas('success');

    expect(LecturerAttendance::where('lecturer_id', $lecturer->id)->count())->toBe(1);
});

it('dosen tidak dapat check-in ke kelas yang bukan diampunya', function () {
    $user = User::factory()->create()->assignRole('dosen');
    Lecturer::factory()->create(['user_id' => $user->id]);
    $otherLecturer = Lecturer::factory()->create();
    $course = Course::factory()->create();
    $offering = CourseOffering::factory()->create([
        'lecturer_id' => $otherLecturer->id,
        'course_id' => $course->id,
    ]);

    $this->actingAs($user)
        ->post(route('dosen.kehadiran.check-in'), [
            'course_offering_id' => $offering->id,
            'date' => now()->toDateString(),
            'check_in' => now()->format('H:i:s'),
        ])
        ->assertForbidden();
});

it('dosen dapat check-out dari kehadiran yang sudah check-in', function () {
    $user = User::factory()->create()->assignRole('dosen');
    $lecturer = Lecturer::factory()->create(['user_id' => $user->id]);
    $course = Course::factory()->create();
    $offering = CourseOffering::factory()->create([
        'lecturer_id' => $lecturer->id,
        'course_id' => $course->id,
    ]);
    $attendance = LecturerAttendance::factory()->create([
        'lecturer_id' => $lecturer->id,
        'course_offering_id' => $offering->id,
        'date' => now()->toDateString(),
        'check_in' => now()->format('H:i:s'),
        'check_out' => null,
        'status' => LecturerAttendanceStatus::Hadir,
    ]);

    $this->actingAs($user)
        ->post(route('dosen.kehadiran.check-out', $attendance->id))
        ->assertSessionHas('success');

    $attendance->refresh();
    expect($attendance->check_out)->not->toBeNull();
});

it('dosen tidak dapat check-out kehadiran milik dosen lain', function () {
    $user = User::factory()->create()->assignRole('dosen');
    Lecturer::factory()->create(['user_id' => $user->id]);
    $otherLecturer = Lecturer::factory()->create();
    $course = Course::factory()->create();
    $offering = CourseOffering::factory()->create([
        'lecturer_id' => $otherLecturer->id,
        'course_id' => $course->id,
    ]);
    $attendance = LecturerAttendance::factory()->create([
        'lecturer_id' => $otherLecturer->id,
        'course_offering_id' => $offering->id,
        'date' => now()->toDateString(),
        'check_in' => now()->format('H:i:s'),
        'check_out' => null,
    ]);

    $this->actingAs($user)
        ->post(route('dosen.kehadiran.check-out', $attendance->id))
        ->assertForbidden();
});

it('super-admin dapat melihat halaman kehadiran dosen', function () {
    $this->seed();

    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $user->assignRole('dosen');
    Lecturer::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('dosen.kehadiran.index'))
        ->assertOk();
});
