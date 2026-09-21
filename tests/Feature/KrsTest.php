<?php

use App\Enums\DayOfWeek;
use App\Enums\StudyPlanDetailStatus;
use App\Enums\StudyPlanStatus;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Semester;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    $this->mahasiswa = User::where('email', 'mahasiswa@siakad.test')->firstOrFail();
    $this->dosen = User::where('email', 'dosen@siakad.test')->firstOrFail();
    $this->semester = Semester::where('is_active', true)->firstOrFail();

    // Ambil course yang sudah ada di seeder.
    $this->course = Course::where('code', 'IF101')->firstOrFail();

    // Buat offering tambahan untuk testing.
    $this->offering = CourseOffering::create([
        'course_id' => $this->course->id,
        'semester_id' => $this->semester->id,
        'lecturer_id' => $this->dosen->lecturer->id,
        'classroom_id' => \App\Models\Classroom::firstOrFail()->id,
        'day' => DayOfWeek::Senin->value,
        'start_time' => '13:00:00',
        'end_time' => '15:00:00',
        'quota' => 30,
    ]);

    // Hapus KRS yang sudah ada dari seeder agar test dimulai dari draft.
    $existingPlan = StudyPlan::where('student_id', $this->mahasiswa->student->id)
        ->where('semester_id', $this->semester->id)
        ->first();

    if ($existingPlan) {
        $existingPlan->studyPlanDetails()->delete();
        $existingPlan->delete();
    }
});

it('mahasiswa dapat melihat halaman KRS', function () {
    $this->actingAs($this->mahasiswa)
        ->get(route('mahasiswa.krs.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Krs')
            ->has('semester')
            ->has('plan')
            ->has('offerings')
            ->has('selected')
            ->has('sks_limit')
            ->has('total_sks'));
});

it('mahasiswa dapat menambah mata kuliah ke KRS', function () {
    $this->actingAs($this->mahasiswa)
        ->post(route('mahasiswa.krs.store'), [
            'course_offering_id' => $this->offering->id,
        ])
        ->assertRedirect();

    $plan = StudyPlan::where('student_id', $this->mahasiswa->student->id)
        ->where('semester_id', $this->semester->id)
        ->firstOrFail();

    $this->assertDatabaseHas('study_plan_details', [
        'study_plan_id' => $plan->id,
        'course_offering_id' => $this->offering->id,
    ]);
});

it('mahasiswa dapat menghapus mata kuliah dari KRS', function () {
    // Tambah dulu.
    $this->actingAs($this->mahasiswa)
        ->post(route('mahasiswa.krs.store'), [
            'course_offering_id' => $this->offering->id,
        ]);

    $plan = StudyPlan::where('student_id', $this->mahasiswa->student->id)
        ->where('semester_id', $this->semester->id)
        ->firstOrFail();

    $detail = StudyPlanDetail::where('study_plan_id', $plan->id)->firstOrFail();

    // Hapus.
    $this->actingAs($this->mahasiswa)
        ->delete(route('mahasiswa.krs.destroy', ['id' => $detail->id]))
        ->assertRedirect();

    $this->assertDatabaseMissing('study_plan_details', ['id' => $detail->id]);
});

it('mahasiswa dapat submit KRS', function () {
    // Tambah mata kuliah dulu.
    $this->actingAs($this->mahasiswa)
        ->post(route('mahasiswa.krs.store'), [
            'course_offering_id' => $this->offering->id,
        ]);

    // Submit.
    $this->actingAs($this->mahasiswa)
        ->post(route('mahasiswa.krs.submit'))
        ->assertRedirect();

    $plan = StudyPlan::where('student_id', $this->mahasiswa->student->id)
        ->where('semester_id', $this->semester->id)
        ->firstOrFail();

    expect($plan->status)->toBe(StudyPlanStatus::Submitted);
});

it('menolak mahasiswa lain menghapus KRS milik orang lain', function () {
    $mahasiswaLain = User::factory()->create([
        'name' => 'Mahasiswa Lain',
        'email' => 'mahasiswa2@siakad.test',
        'password' => 'password',
    ]);
    $mahasiswaLain->syncRoles(['mahasiswa']);

    $studentLain = \App\Models\Student::factory()->create([
        'user_id' => $mahasiswaLain->id,
        'study_program_id' => $this->mahasiswa->student->study_program_id,
    ]);

    // Buat KRS untuk mahasiswa lain.
    $planLain = StudyPlan::create([
        'student_id' => $studentLain->id,
        'semester_id' => $this->semester->id,
        'status' => 'draft',
    ]);

    $detailLain = StudyPlanDetail::create([
        'study_plan_id' => $planLain->id,
        'course_offering_id' => $this->offering->id,
        'status' => 'pending',
    ]);

    // Coba hapus sebagai mahasiswa utama -> seharusnya 403 atau redirect dengan error.
    $this->actingAs($this->mahasiswa)
        ->delete(route('mahasiswa.krs.destroy', ['id' => $detailLain->id]))
        ->assertRedirect();

    $this->assertDatabaseHas('study_plan_details', ['id' => $detailLain->id]);
});

it('menolak tambah mata kuliah duplikat', function () {
    $this->actingAs($this->mahasiswa)
        ->post(route('mahasiswa.krs.store'), [
            'course_offering_id' => $this->offering->id,
        ]);

    // Tambah lagi offering yang sama.
    $this->actingAs($this->mahasiswa)
        ->post(route('mahasiswa.krs.store'), [
            'course_offering_id' => $this->offering->id,
        ])
        ->assertRedirect();

    $plan = StudyPlan::where('student_id', $this->mahasiswa->student->id)
        ->where('semester_id', $this->semester->id)
        ->firstOrFail();

    // Hanya boleh ada 1 detail untuk offering yang sama.
    expect(StudyPlanDetail::where('study_plan_id', $plan->id)
        ->where('course_offering_id', $this->offering->id)
        ->count()
    )->toBe(1);
});

it('menolak submit KRS tanpa mata kuliah', function () {
    $this->actingAs($this->mahasiswa)
        ->post(route('mahasiswa.krs.submit'))
        ->assertRedirect();

    $plan = StudyPlan::where('student_id', $this->mahasiswa->student->id)
        ->where('semester_id', $this->semester->id)
        ->first();

    // Plan mungkin tidak dibuat karena tidak ada mata kuliah yang ditambahkan.
    // Jika ada, status harus tetap draft.
    if ($plan) {
        expect($plan->status)->toBe(StudyPlanStatus::Draft);
    } else {
        // Jika tidak ada plan, berarti submit gagal seperti yang diharapkan.
        expect(true)->toBeTrue();
    }
});
