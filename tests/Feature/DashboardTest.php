<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('menampilkan dashboard untuk setiap peran', function (string $email, string $path, string $component) {
    $user = User::where('email', $email)->firstOrFail();

    $this->actingAs($user)
        ->get($path)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component($component)
            ->has('stats'));
})->with([
    'super-admin' => ['admin@siakad.test', '/admin/dashboard', 'Admin/Dashboard'],
    'kaprodi' => ['kaprodi@siakad.test', '/kaprodi/dashboard', 'Kaprodi/Dashboard'],
    'dosen' => ['dosen@siakad.test', '/dosen/dashboard', 'Dosen/Dashboard'],
    'mahasiswa' => ['mahasiswa@siakad.test', '/mahasiswa/dashboard', 'Mahasiswa/Dashboard'],
    'pimpinan' => ['pimpinan@siakad.test', '/pimpinan/dashboard', 'Pimpinan/Dashboard'],
]);

it('memuat statistik super-admin', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Dashboard')
            ->has('stats.total_users')
            ->has('stats.total_lecturers')
            ->has('stats.total_students')
            ->has('stats.total_courses')
            ->has('stats.recent_activities'));
});

it('memuat statistik pimpinan dengan agregat', function () {
    $user = User::where('email', 'pimpinan@siakad.test')->firstOrFail();

    $this->actingAs($user)
        ->get('/pimpinan/dashboard')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Pimpinan/Dashboard')
            ->has('stats.total_students')
            ->has('stats.average_gpa')
            ->has('stats.grade_distribution')
            ->has('stats.gpa_trend'));
});

it('menolak akses dashboard milik peran lain', function () {
    $mahasiswa = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();

    $this->actingAs($mahasiswa)
        ->get('/admin/dashboard')
        ->assertForbidden();
});

it('mengarahkan tamu ke halaman login', function () {
    $this->get('/admin/dashboard')->assertRedirect('/login');
});
