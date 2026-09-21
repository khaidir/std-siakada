<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Otorisasi via Policy.
    }

    public function rules(): array
    {
        $userId = $this->route('id');
        $isCreate = $userId === null;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'role' => ['required', 'string', Rule::in(['super-admin', 'kaprodi', 'dosen', 'mahasiswa', 'pimpinan'])],
        ];

        if ($isCreate) {
            $rules['password'] = ['required', 'string', 'min:8'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8'];
        }

        // Validasi bersyarat untuk role dosen.
        $role = $this->input('role');

        if ($role === 'dosen') {
            $rules['nidn'] = ['required', 'string', 'max:20'];
            $rules['study_program_id'] = ['required', 'integer', 'exists:study_programs,id'];
            $rules['academic_rank'] = ['required', 'string', Rule::in(['asisten_ahli', 'lektor', 'lektor_kepala', 'guru_besar'])];
        }

        if ($role === 'mahasiswa') {
            $rules['nim'] = ['required', 'string', 'max:20'];
            $rules['study_program_id'] = ['required', 'integer', 'exists:study_programs,id'];
            $rules['entry_year'] = ['required', 'string', 'max:4'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'nidn.required' => 'NIDN wajib diisi untuk dosen.',
            'study_program_id.required' => 'Program Studi wajib dipilih.',
            'study_program_id.exists' => 'Program Studi tidak valid.',
            'academic_rank.required' => 'Pangkat Akademik wajib dipilih untuk dosen.',
            'academic_rank.in' => 'Pangkat Akademik tidak valid.',
            'nim.required' => 'NIM wajib diisi untuk mahasiswa.',
            'entry_year.required' => 'Angkatan wajib diisi untuk mahasiswa.',
        ];
    }
}
