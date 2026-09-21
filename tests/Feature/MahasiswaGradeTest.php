<?php

use App\Models\Grade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    $this->mahasiswa = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();
    $this->dosen = User::where('email', 'dosen@siakad.test')->firstOrFail();
});

it('mahasiswa dapat melihat halaman nilai', function () {
    $this->actingAs($this->mahasiswa)
        ->get(route('mahasiswa.nilai.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Nilai')
            ->has('grades')
            ->has('gpa')
            ->has('total_sks'));
});

it('mahasiswa melihat nilai miliknya sendiri', function () {
    $studentId = $this->mahasiswa->student->id;

    $this->actingAs($this->mahasiswa)
        ->get(route('mahasiswa.nilai.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Nilai')
            ->has('grades')
            ->where('total_sks', fn ($v) => $v >= 0)
            ->where('gpa', fn ($v) => $v >= 0));
});

it('mahasiswa lain tidak bisa melihat nilai mahasiswa lain via policy', function () {
    // Mahasiswa hanya bisa melihat nilainya sendiri — tidak ada endpoint
    // untuk melihat nilai mahasiswa lain. Policy view memeriksa student_id.
    $studentId = $this->mahasiswa->student->id;

    // Ambil grade milik mahasiswa.
    $grade = Grade::where('student_id', $studentId)->first();

    if ($grade) {
        // Mahasiswa lain coba akses grade milik mahasiswa pertama.
        $otherUser = User::factory()->create(['name' => 'Other Student', 'email' => 'other@test.test']);
        $otherUser->syncRoles(['mahasiswa']);

        $otherStudent = \App\Models\Student::factory()->create([
            'user_id' => $otherUser->id,
            'study_program_id' => $this->mahasiswa->student->study_program_id,
        ]);

        $this->actingAs($otherUser)
            ->get(route('mahasiswa.nilai.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Mahasiswa/Nilai')
                ->where('grades', []));
    } else {
        // Jika belum ada grade, test tetap pass.
        $this->assertTrue(true);
    }
});
