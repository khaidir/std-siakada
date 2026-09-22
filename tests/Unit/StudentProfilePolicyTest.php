<?php

use App\Models\Student;
use App\Models\User;
use App\Policies\StudentPolicy;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->policy = new StudentPolicy;
});

it('mengizinkan mahasiswa mengubah barisnya sendiri', function () {
    $user = User::factory()->create();
    $user->assignRole('mahasiswa');
    $student = Student::factory()->create(['user_id' => $user->id]);

    expect($this->policy->update($user, $student))->toBeTrue();
});

it('menolak mahasiswa mengubah baris mahasiswa lain', function () {
    $user = User::factory()->create();
    $user->assignRole('mahasiswa');
    Student::factory()->create(['user_id' => $user->id]);

    $lainUser = User::factory()->create();
    $lainUser->assignRole('mahasiswa');
    $lain = Student::factory()->create(['user_id' => $lainUser->id]);

    expect($this->policy->update($user, $lain))->toBeFalse();
});

it('menolak dosen mengubah baris mahasiswa', function () {
    $dosen = User::factory()->create();
    $dosen->assignRole('dosen');

    $mhsUser = User::factory()->create();
    $mhsUser->assignRole('mahasiswa');
    $student = Student::factory()->create(['user_id' => $mhsUser->id]);

    expect($this->policy->update($dosen, $student))->toBeFalse();
});
