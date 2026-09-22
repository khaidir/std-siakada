<?php

use App\Enums\Gender;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->user = User::factory()->create(['name' => 'Karla Ayu', 'email' => 'karla@example.test']);
    $this->user->assignRole('mahasiswa');

    $this->student = Student::factory()->create([
        'user_id' => $this->user->id,
        'nim' => '1301223001',
        'gpa' => 3.45,
        'total_sks' => 110,
    ]);
});

/** Payload valid minimal yang dipakai berulang. */
function validProfilePayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Karla Ayu Pratiwi',
        'email' => 'karla.baru@example.test',
        'birth_place' => 'Bandung',
        'birth_date' => '2003-05-17',
        'gender' => Gender::Perempuan->value,
        'address' => 'Jl. Telekomunikasi No. 1',
        'phone' => '081234567890',
    ], $overrides);
}

it('menampilkan halaman profil beserta props yang dibutuhkan', function () {
    $this->actingAs($this->user)
        ->get(route('mahasiswa.profil.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Mahasiswa/Profile')
            ->where('profile.nim', '1301223001')
            ->where('profile.name', 'Karla Ayu')
            ->has('genderOptions', 2)
        );
});

it('menyimpan perubahan biodata milik sendiri', function () {
    $this->actingAs($this->user)
        ->put(route('mahasiswa.profil.update'), validProfilePayload())
        ->assertRedirect(route('mahasiswa.profil.index'));

    $this->user->refresh();
    $this->student->refresh();

    expect($this->user->name)->toBe('Karla Ayu Pratiwi')
        ->and($this->user->email)->toBe('karla.baru@example.test')
        ->and($this->student->birth_place)->toBe('Bandung')
        ->and($this->student->birth_date->format('Y-m-d'))->toBe('2003-05-17')
        ->and($this->student->gender)->toBe(Gender::Perempuan)
        ->and($this->student->phone)->toBe('081234567890');
});

it('menolak email yang sudah dipakai pengguna lain', function () {
    User::factory()->create(['email' => 'sudah@example.test']);

    $this->actingAs($this->user)
        ->put(route('mahasiswa.profil.update'), validProfilePayload(['email' => 'sudah@example.test']))
        ->assertSessionHasErrors('email');
});

it('menerima email milik sendiri tanpa dianggap duplikat', function () {
    $this->actingAs($this->user)
        ->put(route('mahasiswa.profil.update'), validProfilePayload(['email' => 'karla@example.test']))
        ->assertSessionHasNoErrors();
});

it('menolak tanggal lahir di masa depan', function () {
    $this->actingAs($this->user)
        ->put(route('mahasiswa.profil.update'), validProfilePayload(['birth_date' => now()->addYear()->format('Y-m-d')]))
        ->assertSessionHasErrors('birth_date');
});

it('menolak nama kosong dan jenis kelamin tidak dikenal', function () {
    $this->actingAs($this->user)
        ->put(route('mahasiswa.profil.update'), validProfilePayload(['name' => '', 'gender' => 'alien']))
        ->assertSessionHasErrors(['name', 'gender']);
});

/*
 * Ini test paling penting: endpoint dikendalikan mahasiswa sendiri, jadi field
 * akademik harus tetap utuh walau dikirim di payload.
 */
it('tidak mengubah field akademik walau dikirim di payload', function () {
    $before = [
        'nim' => $this->student->nim,
        'gpa' => (string) $this->student->gpa,
        'total_sks' => $this->student->total_sks,
        'status' => $this->student->status,
        'study_program_id' => $this->student->study_program_id,
        'entry_year' => $this->student->entry_year,
    ];

    $this->actingAs($this->user)
        ->put(route('mahasiswa.profil.update'), validProfilePayload([
            'nim' => '9999999999',
            'gpa' => 4.00,
            'total_sks' => 999,
            'status' => 'lulus',
            'study_program_id' => 99999,
            'entry_year' => '1999',
        ]))
        ->assertRedirect();

    $this->student->refresh();

    expect($this->student->nim)->toBe($before['nim'])
        ->and((string) $this->student->gpa)->toBe($before['gpa'])
        ->and($this->student->total_sks)->toBe($before['total_sks'])
        ->and($this->student->status)->toBe($before['status'])
        ->and($this->student->study_program_id)->toBe($before['study_program_id'])
        ->and($this->student->entry_year)->toBe($before['entry_year']);
});

it('menolak akses dari peran selain mahasiswa', function () {
    $dosenUser = User::factory()->create();
    $dosenUser->assignRole('dosen');
    Lecturer::factory()->create(['user_id' => $dosenUser->id]);

    $this->actingAs($dosenUser)
        ->get(route('mahasiswa.profil.index'))
        ->assertForbidden();
});

it('mengalihkan tamu ke halaman login', function () {
    $this->get(route('mahasiswa.profil.index'))->assertRedirect(route('login'));
});

it('mengembalikan 404 bila pengguna mahasiswa tidak punya baris student', function () {
    $tanpaProfil = User::factory()->create();
    $tanpaProfil->assignRole('mahasiswa');

    $this->actingAs($tanpaProfil)
        ->get(route('mahasiswa.profil.index'))
        ->assertNotFound();
});
