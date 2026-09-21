<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignThesisSupervisorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supervisor_1_id' => ['required', 'exists:lecturers,id'],
            'supervisor_2_id' => ['nullable', 'exists:lecturers,id'],
        ];
    }
}
