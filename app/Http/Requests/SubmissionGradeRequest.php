<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use Illuminate\Foundation\Http\FormRequest;

class SubmissionGradeRequest extends FormRequest
{
    /**
     * Dosen pengampu kelas yang boleh menilai submission.
     */
    public function authorize(): bool
    {
        $lecturer = $this->user()?->lecturer;

        if (! $lecturer) {
            return false;
        }

        $assignmentId = (int) $this->route('assignment');
        $assignment = Assignment::query()->findOrFail($assignmentId);

        return $assignment->courseOffering?->lecturer_id === $lecturer->id;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'grades' => ['required', 'array', 'min:1'],
            'grades.*.submission_id' => ['required', 'integer', 'exists:assignment_submissions,id'],
            'grades.*.score' => ['required', 'numeric', 'min:0'],
            'grades.*.feedback' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
