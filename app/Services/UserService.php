<?php

namespace App\Services;

use App\DTO\UserData;
use App\Repositories\Contracts\LecturerRepository;
use App\Repositories\Contracts\StudentRepository;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly StudentRepository $students,
        private readonly LecturerRepository $lecturers,
    ) {}

    /**
     * Data halaman daftar pengguna.
     *
     * @return array<string, mixed>
     */
    public function pageData(): array
    {
        $users = $this->users->listWithRole();

        $items = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name ?? '',
                'created_at' => $user->created_at?->format('d M Y'),
            ];
        })->values()->all();

        return [
            'users' => $items,
            'study_programs' => \App\Models\StudyProgram::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get()
                ->map(fn ($sp) => ['id' => $sp->id, 'name' => $sp->name]),
        ];
    }

    /**
     * Buat user baru + profil (student/lecturer) + assign role.
     */
    public function create(UserData $data): void
    {
        DB::transaction(function () use ($data) {
            // Cek email unik.
            $existing = $this->users->findByEmail($data->email);

            if ($existing) {
                throw ValidationException::withMessages([
                    'email' => 'Email sudah digunakan.',
                ]);
            }

            $user = $this->users->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => Hash::make($data->password),
            ]);

            // Assign role.
            $user->assignRole($data->role);

            // Buat profil sesuai role.
            if ($data->role === 'mahasiswa') {
                if (! $data->nim || ! $data->study_program_id || ! $data->entry_year) {
                    throw ValidationException::withMessages([
                        'nim' => 'NIM, Program Studi, dan Angkatan wajib untuk mahasiswa.',
                    ]);
                }

                $this->students->create([
                    'user_id' => $user->id,
                    'nim' => $data->nim,
                    'study_program_id' => $data->study_program_id,
                    'entry_year' => $data->entry_year,
                    'status' => 'aktif',
                ]);
            }

            if ($data->role === 'dosen') {
                if (! $data->nidn || ! $data->study_program_id || ! $data->academic_rank) {
                    throw ValidationException::withMessages([
                        'nidn' => 'NIDN, Program Studi, dan Pangkat Akademik wajib untuk dosen.',
                    ]);
                }

                $this->lecturers->create([
                    'user_id' => $user->id,
                    'nidn' => $data->nidn,
                    'study_program_id' => $data->study_program_id,
                    'academic_rank' => $data->academic_rank,
                ]);
            }
        });
    }

    /**
     * Update user + profil.
     */
    public function update(int $id, UserData $data): void
    {
        DB::transaction(function () use ($id, $data) {
            $user = $this->users->findById($id);

            if (! $user) {
                throw ValidationException::withMessages([
                    'id' => 'Pengguna tidak ditemukan.',
                ]);
            }

            // Cek email unik (kecuali milik sendiri).
            $existing = $this->users->findByEmail($data->email);

            if ($existing && $existing->id !== $id) {
                throw ValidationException::withMessages([
                    'email' => 'Email sudah digunakan.',
                ]);
            }

            $updateData = [
                'name' => $data->name,
                'email' => $data->email,
            ];

            if ($data->password) {
                $updateData['password'] = Hash::make($data->password);
            }

            $this->users->update($id, $updateData);

            // Sync role.
            $user->syncRoles([$data->role]);

            // Update/sync profil.
            if ($data->role === 'mahasiswa') {
                $student = $user->student;

                if ($student) {
                    $student->update([
                        'nim' => $data->nim ?? $student->nim,
                        'study_program_id' => $data->study_program_id ?? $student->study_program_id,
                        'entry_year' => $data->entry_year ?? $student->entry_year,
                    ]);
                }
            }

            if ($data->role === 'dosen') {
                $lecturer = $user->lecturer;

                if ($lecturer) {
                    $lecturer->update([
                        'nidn' => $data->nidn ?? $lecturer->nidn,
                        'study_program_id' => $data->study_program_id ?? $lecturer->study_program_id,
                        'academic_rank' => $data->academic_rank ?? $lecturer->academic_rank,
                    ]);
                }
            }
        });
    }

    /**
     * Hapus user.
     */
    public function delete(int $id): void
    {
        $this->users->delete($id);
    }
}
