<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateThesisStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:proposal,seminar_proposal,sidang,lulus,revisi'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status harus diisi.',
            'status.in' => 'Status tidak valid.',
        ];
    }
}
