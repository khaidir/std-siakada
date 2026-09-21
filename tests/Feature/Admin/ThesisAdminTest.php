<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('super-admin dapat melihat halaman manajemen skripsi', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.skripsi.index'))
        ->assertOk();
});

it('super-admin dapat assign pembimbing skripsi', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $thesis = \App\Models\Thesis::first();

    if (! $thesis) {
        $this->markTestSkipped('Tidak ada data skripsi.');
    }

    $dosen = \App\Models\Lecturer::first();

    $this->actingAs($user)
        ->post(route('admin.skripsi.assign', $thesis->id), [
            'supervisor_1_id' => $dosen->id,
            'supervisor_2_id' => null,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('theses', [
        'id' => $thesis->id,
        'supervisor_1_id' => $dosen->id,
    ]);
});

it('super-admin dapat update status skripsi', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $thesis = \App\Models\Thesis::first();

    if (! $thesis) {
        $this->markTestSkipped('Tidak ada data skripsi.');
    }

    $this->actingAs($user)
        ->put(route('admin.skripsi.update-status', $thesis->id), [
            'status' => 'sidang',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('theses', [
        'id' => $thesis->id,
        'status' => 'sidang',
    ]);
});

it('dosen tidak dapat mengakses manajemen skripsi', function () {
    $user = User::where('email', 'dosen@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.skripsi.index'))
        ->assertForbidden();
});

it('mahasiswa tidak dapat mengakses manajemen skripsi', function () {
    $user = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.skripsi.index'))
        ->assertForbidden();
});
