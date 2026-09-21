<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Otorisasi dilakukan di Policy/Service.
    }

    public function rules(): array
    {
        $courseId = $this->route('id');

        return [
            'study_program_id' => ['sometimes', 'integer', 'exists:study_programs,id'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('courses', 'code')->ignore($courseId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'sks' => ['required', 'integer', 'min:1', 'max:24'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'type' => ['required', 'string', Rule::in(['wajib', 'pilihan'])],
        ];
    }

    public function messages(): array
    {
        return [
            'study_program_id.required' => 'Program Studi wajib dipilih.',
            'study_program_id.exists' => 'Program Studi tidak valid.',
            'code.required' => 'Kode mata kuliah wajib diisi.',
            'code.unique' => 'Kode mata kuliah sudah digunakan.',
            'name.required' => 'Nama mata kuliah wajib diisi.',
            'sks.required' => 'SKS wajib diisi.',
            'sks.min' => 'SKS minimal 1.',
            'sks.max' => 'SKS maksimal 24.',
            'semester.required' => 'Semester wajib diisi.',
            'type.required' => 'Tipe mata kuliah wajib dipilih.',
            'type.in' => 'Tipe mata kuliah harus wajib atau pilihan.',
        ];
    }
}
