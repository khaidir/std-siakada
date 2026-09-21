<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('classrooms', 'code')->ignore($id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:9999'],
            'building' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode ruangan wajib diisi.',
            'code.unique' => 'Kode ruangan sudah digunakan.',
            'name.required' => 'Nama ruangan wajib diisi.',
            'capacity.required' => 'Kapasitas wajib diisi.',
            'capacity.min' => 'Kapasitas minimal 1.',
        ];
    }
}
