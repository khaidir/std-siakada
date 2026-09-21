<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveThesisLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'log_id' => ['required', 'integer', 'exists:thesis_logs,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'log_id.required' => 'Log harus diisi.',
            'log_id.exists' => 'Log tidak ditemukan.',
        ];
    }
}
