<?php

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('menyiapkan roles dan permissions sesuai RBAC', function () {
    $this->seed();

    expect(Role::pluck('name')->sort()->values()->all())
        ->toBe(['dosen', 'kaprodi', 'mahasiswa', 'pimpinan', 'super-admin'])
        ->and(Permission::count())->toBe(39);

    $admin = User::where('email', 'admin@siakad.test')->firstOrFail();
    expect($admin->hasRole('super-admin'))->toBeTrue()
        ->and($admin->getAllPermissions())->toHaveCount(39);
});

it('menyiapkan master data sesuai PRD', function () {
    $this->seed();

    expect(Faculty::count())->toBe(2)
        ->and(StudyProgram::count())->toBe(6)
        ->and(Course::count())->toBe(19)
        ->and(Classroom::count())->toBe(2)
        ->and(AcademicYear::count())->toBe(1)
        ->and(Semester::count())->toBe(2)
        ->and(\App\Models\CourseOffering::count())->toBe(38);
});

it('menyiapkan akun default beserta profil dan data akademik mahasiswa', function () {
    $this->seed();

    expect(User::where('email', 'kaprodi@siakad.test')->first()->hasRole('kaprodi'))->toBeTrue()
        ->and(User::where('email', 'dosen@siakad.test')->first()->hasRole('dosen'))->toBeTrue()
        ->and(User::where('email', 'pimpinan@siakad.test')->first()->hasRole('pimpinan'))->toBeTrue();

    $mhs = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();
    expect($mhs->hasRole('mahasiswa'))->toBeTrue();

    $student = $mhs->student;
    expect($student)->not->toBeNull()
        ->and($student->studyPlans)->toHaveCount(1)
        ->and($student->studyPlans->first()->status->value)->toBe('approved')
        ->and($student->studyPlans->first()->studyPlanDetails)->not->toBeEmpty()
        ->and($student->grades)->not->toBeEmpty()
        ->and($student->attendances)->not->toBeEmpty()
        ->and($student->thesis)->not->toBeNull()
        ->and($student->internship)->not->toBeNull();

    $dosen = User::where('email', 'dosen@siakad.test')->firstOrFail();
    expect($dosen->lecturer)->not->toBeNull()
        ->and($student->thesis->supervisorOne->is($dosen->lecturer))->toBeTrue();
});

it('idempoten saat dijalankan dua kali', function () {
    $this->seed();
    $this->seed();

    expect(Faculty::count())->toBe(2)
        ->and(StudyProgram::count())->toBe(6)
        ->and(Course::count())->toBe(19)
        ->and(Role::count())->toBe(5)
        ->and(Permission::count())->toBe(39)
        ->and(User::where('email', 'admin@siakad.test')->count())->toBe(1)
        ->and(\App\Models\CourseOffering::count())->toBe(38);
});
