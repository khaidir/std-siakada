<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('mengarahkan pengguna ke dashboard sesuai peran setelah login', function () {
    $cases = [
        ['admin@siakad.test', '/admin/dashboard'],
        ['kaprodi@siakad.test', '/kaprodi/dashboard'],
        ['dosen@siakad.test', '/dosen/dashboard'],
        ['mahasiswa@siakad.test', '/mahasiswa/dashboard'],
        ['pimpinan@siakad.test', '/pimpinan/dashboard'],
    ];

    foreach ($cases as [$email, $path]) {
        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'password',
        ]);

        $response->assertRedirect($path);
        $this->assertAuthenticated();

        $this->post('/logout');
        $this->assertGuest();
    }
});

it('menampilkan error saat kredensial salah', function () {
    $response = $this->from('/login')->post('/login', [
        'email' => 'admin@siakad.test',
        'password' => 'salah',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('mengarahkan ke halaman login setelah logout', function () {
    $user = User::where('email', 'admin@siakad.test')->firstOrFail();
    $this->actingAs($user);

    $response = $this->post('/logout');

    $response->assertRedirect('/login');
    $this->assertGuest();
});
