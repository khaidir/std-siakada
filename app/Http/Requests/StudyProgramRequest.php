<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudyProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'faculty_id' => ['required', 'integer', 'exists:faculties,id'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('study_programs', 'code')->ignore($id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'degree_level' => ['required', 'string', Rule::in(['d3', 'd4', 's1', 's2', 's3', 'profesi'])],
        ];
    }

    public function messages(): array
    {
        return [
            'faculty_id.required' => 'Fakultas wajib dipilih.',
            'faculty_id.exists' => 'Fakultas tidak valid.',
            'code.required' => 'Kode program studi wajib diisi.',
            'code.unique' => 'Kode program studi sudah digunakan.',
            'name.required' => 'Nama program studi wajib diisi.',
            'degree_level.required' => 'Jenjang wajib dipilih.',
            'degree_level.in' => 'Jenjang tidak valid.',
        ];
    }
}
