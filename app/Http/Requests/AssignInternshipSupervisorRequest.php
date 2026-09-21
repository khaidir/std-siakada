<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignInternshipSupervisorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supervisor_id' => ['required', 'exists:lecturers,id'],
        ];
    }
}
