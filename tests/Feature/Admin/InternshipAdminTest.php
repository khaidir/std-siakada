<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('super-admin dapat melihat halaman manajemen KP', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.kp.index'))
        ->assertOk();
});

it('super-admin dapat assign pembimbing KP', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $internship = \App\Models\Internship::first();

    if (! $internship) {
        $this->markTestSkipped('Tidak ada data KP.');
    }

    $dosen = \App\Models\Lecturer::first();

    $this->actingAs($user)
        ->post(route('admin.kp.assign', $internship->id), [
            'supervisor_id' => $dosen->id,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('internships', [
        'id' => $internship->id,
        'supervisor_id' => $dosen->id,
    ]);
});

it('super-admin dapat update status KP', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $internship = \App\Models\Internship::first();

    if (! $internship) {
        $this->markTestSkipped('Tidak ada data KP.');
    }

    $this->actingAs($user)
        ->put(route('admin.kp.update-status', $internship->id), [
            'status' => 'selesai',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('internships', [
        'id' => $internship->id,
        'status' => 'selesai',
    ]);
});

it('dosen tidak dapat mengakses manajemen KP', function () {
    $user = User::where('email', 'dosen@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.kp.index'))
        ->assertForbidden();
});

it('mahasiswa tidak dapat mengakses manajemen KP', function () {
    $user = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.kp.index'))
        ->assertForbidden();
});
