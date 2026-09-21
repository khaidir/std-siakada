<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KrsMonitoringRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fakultas' => ['nullable', 'integer', 'exists:faculties,id'],
            'prodi' => ['nullable', 'integer', 'exists:study_programs,id'],
            'semester' => ['nullable', 'integer', 'exists:semesters,id'],
            'status' => ['nullable', 'string', 'in:draft,submitted,approved,rejected'],
        ];
    }
}
