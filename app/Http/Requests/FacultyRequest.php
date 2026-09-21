<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FacultyRequest extends FormRequest
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
                Rule::unique('faculties', 'code')->ignore($id),
            ],
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode fakultas wajib diisi.',
            'code.unique' => 'Kode fakultas sudah digunakan.',
            'name.required' => 'Nama fakultas wajib diisi.',
        ];
    }
}
