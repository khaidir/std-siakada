<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('super-admin dapat melihat halaman monitoring KRS', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.krs-monitoring.index'))
        ->assertOk();
});

it('super-admin dapat melihat detail KRS', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $plan = \App\Models\StudyPlan::first();

    if (! $plan) {
        $this->markTestSkipped('Tidak ada data KRS.');
    }

    $this->actingAs($user)
        ->get(route('admin.krs-monitoring.show', $plan->id))
        ->assertOk();
});

it('dosen tidak dapat mengakses monitoring KRS', function () {
    $user = User::where('email', 'dosen@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.krs-monitoring.index'))
        ->assertForbidden();
});

it('mahasiswa tidak dapat mengakses monitoring KRS', function () {
    $user = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.krs-monitoring.index'))
        ->assertForbidden();
});
