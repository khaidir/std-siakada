<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    $this->mahasiswa = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();
});

it('mahasiswa dapat melihat halaman transkrip', function () {
    $this->actingAs($this->mahasiswa)
        ->get(route('mahasiswa.transkrip.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Transkrip')
            ->has('student')
            ->has('semesterGroups')
            ->has('ipk')
            ->has('totalSks'));
});

it('transkrip menampilkan data per semester', function () {
    $this->actingAs($this->mahasiswa)
        ->get(route('mahasiswa.transkrip.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Transkrip')
            ->has('semesterGroups', fn (Assert $groups) => $groups
                ->each(fn (Assert $group) => $group
                    ->has('semester')
                    ->has('grades')
                    ->has('ip')
                    ->has('total_sks'))));
});

it('IPK kumulatif antara 0 dan 4', function () {
    $this->actingAs($this->mahasiswa)
        ->get(route('mahasiswa.transkrip.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Transkrip')
            ->where('ipk', fn ($v) => $v >= 0 && $v <= 4));
});

it('dosen tidak bisa mengakses halaman transkrip', function () {
    $dosen = User::where('email', 'dosen@siakad.test')->firstOrFail();

    $this->actingAs($dosen)
        ->get(route('mahasiswa.transkrip.index'))
        ->assertForbidden();
});

it('super-admin tidak bisa mengakses halaman transkrip', function () {
    $admin = User::where('email', 'admin@siakad.test')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('mahasiswa.transkrip.index'))
        ->assertForbidden();
});
