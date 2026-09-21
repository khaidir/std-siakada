<?php

use App\Models\CourseOffering;
use App\Models\Lecturer;
use App\Models\LecturerAttendance;
use App\Models\Semester;
use App\Models\StudyProgram;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    // Buat prodi
    $this->prodi = StudyProgram::factory()->create(['name' => 'Teknik Informatika']);
    $this->prodiLain = StudyProgram::factory()->create(['name' => 'Sistem Informasi']);

    // Buat dosen di prodi TI
    $userDosen = User::factory()->create(['email' => 'dosen_ti@test.com']);
    $this->dosen = Lecturer::factory()->create([
        'user_id' => $userDosen->id,
        'study_program_id' => $this->prodi->id,
        'nidn' => '1234567890',
    ]);

    // Buat dosen di prodi SI
    $userDosenSi = User::factory()->create(['email' => 'dosen_si@test.com']);
    $this->dosenSi = Lecturer::factory()->create([
        'user_id' => $userDosenSi->id,
        'study_program_id' => $this->prodiLain->id,
        'nidn' => '0987654321',
    ]);

    // Buat semester
    $this->semester = Semester::factory()->create(['is_active' => true]);

    // Buat course offering untuk dosen TI
    $this->offering = CourseOffering::factory()->create([
        'lecturer_id' => $this->dosen->id,
        'semester_id' => $this->semester->id,
    ]);

    // Buat attendance records untuk dosen TI
    LecturerAttendance::factory()->create([
        'lecturer_id' => $this->dosen->id,
        'course_offering_id' => $this->offering->id,
        'date' => '2025-03-01',
        'check_in' => '07:30',
        'check_out' => '09:00',
        'status' => 'hadir',
    ]);
    LecturerAttendance::factory()->create([
        'lecturer_id' => $this->dosen->id,
        'course_offering_id' => $this->offering->id,
        'date' => '2025-03-08',
        'check_in' => '07:45',
        'check_out' => '09:00',
        'status' => 'terlambat',
    ]);
    LecturerAttendance::factory()->create([
        'lecturer_id' => $this->dosen->id,
        'course_offering_id' => $this->offering->id,
        'date' => '2025-03-15',
        'check_in' => null,
        'check_out' => null,
        'status' => 'alpha',
    ]);

    // Buat attendance records untuk dosen SI
    $offeringSi = CourseOffering::factory()->create([
        'lecturer_id' => $this->dosenSi->id,
        'semester_id' => $this->semester->id,
    ]);
    LecturerAttendance::factory()->create([
        'lecturer_id' => $this->dosenSi->id,
        'course_offering_id' => $offeringSi->id,
        'date' => '2025-03-02',
        'check_in' => '07:30',
        'check_out' => '09:00',
        'status' => 'hadir',
    ]);

    // Buat super-admin
    $this->admin = User::factory()->create(['email' => 'admin_kehadiran@test.com']);
    $this->admin->assignRole('super-admin');
    $this->admin->givePermissionTo('lecturer-attendance.view');

    // Buat kaprodi TI
    $this->kaprodi = User::factory()->create(['email' => 'kaprodi_ti@test.com']);
    $this->kaprodi->assignRole('kaprodi');
    $this->kaprodi->givePermissionTo('lecturer-attendance.view');
    // Set study_program_id di lecturer record kaprodi
    Lecturer::factory()->create([
        'user_id' => $this->kaprodi->id,
        'study_program_id' => $this->prodi->id,
    ]);
});

it('admin dapat melihat halaman monitoring kehadiran dosen', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.kehadiran.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/KehadiranDosen')
            ->has('summary')
            ->has('stats')
            ->has('semesters')
            ->has('study_programs')
        );
});

it('admin dapat melihat detail kehadiran dosen', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.kehadiran.show', $this->dosen->id))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/KehadiranDosenDetail')
            ->has('detail')
        );
});

it('admin melihat semua dosen dari semua prodi', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.kehadiran.index'))
        ->assertInertia(fn ($page) => $page
            ->where('summary', fn ($summary) => count($summary) === 2)
        );
});

it('kaprodi dapat melihat halaman monitoring kehadiran', function () {
    $this->actingAs($this->kaprodi)
        ->get(route('kaprodi.kehadiran.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Kaprodi/KehadiranDosen')
            ->has('summary')
            ->has('stats')
        );
});

it('kaprodi hanya melihat dosen di prodi-nya sendiri', function () {
    $this->actingAs($this->kaprodi)
        ->get(route('kaprodi.kehadiran.index'))
        ->assertInertia(fn ($page) => $page
            ->where('summary', fn ($summary) => count($summary) === 1)
        );
});

it('kaprodi dapat melihat detail kehadiran dosen', function () {
    $this->actingAs($this->kaprodi)
        ->get(route('kaprodi.kehadiran.show', $this->dosen->id))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Kaprodi/KehadiranDosenDetail')
            ->has('detail')
        );
});

it('role lain tidak bisa mengakses halaman monitoring kehadiran', function () {
    $user = User::factory()->create(['email' => 'mahasiswa@test.com']);
    $user->assignRole('mahasiswa');

    $this->actingAs($user)
        ->get(route('admin.kehadiran.index'))
        ->assertForbidden();
});

it('menghitung persentase kehadiran dengan benar', function () {
    // Dosen TI: 3 pertemuan, 1 hadir, 1 terlambat, 1 alpha
    // Persentase = (1+1)/3 * 100 = 66.67
    $this->actingAs($this->admin)
        ->get(route('admin.kehadiran.index'))
        ->assertInertia(fn ($page) => $page
            ->has('summary')
        );

    // Ambil data summary langsung dari response
    $response = $this->actingAs($this->admin)->get(route('admin.kehadiran.index'));
    $summary = $response->original->getData()['page']['props']['summary'] ?? [];
    $dosen = collect($summary)->firstWhere('lecturer_id', $this->dosen->id);

    expect($dosen)->not->toBeNull();
    expect($dosen['persentase'])->toBe(66.67);
});
