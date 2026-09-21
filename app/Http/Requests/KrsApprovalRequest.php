<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KrsApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'string', 'in:approved,rejected'],
            'reason' => ['nullable', 'string', 'max:500', 'required_if:decision,rejected'],
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required' => 'Keputusan harus diisi.',
            'decision.in' => 'Keputusan harus approved atau rejected.',
            'reason.required_if' => 'Alasan penolakan harus diisi.',
            'reason.max' => 'Alasan maksimal 500 karakter.',
        ];
    }
}
