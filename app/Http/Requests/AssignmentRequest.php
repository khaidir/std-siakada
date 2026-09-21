<?php

namespace App\Http\Requests;

use App\Models\CourseOffering;
use Illuminate\Foundation\Http\FormRequest;

class AssignmentRequest extends FormRequest
{
    /**
     * Hanya dosen pengampu kelas yang boleh mengelola tugas.
     */
    public function authorize(): bool
    {
        $lecturer = $this->user()?->lecturer;

        if (! $lecturer) {
            return false;
        }

        // Untuk update, cek via route {id} — cari tugas lalu cek kepemilikan kelas.
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $assignment = \App\Models\Assignment::query()->findOrFail(
                (int) $this->route('id'),
            );

            return $assignment->courseOffering?->lecturer_id === $lecturer->id;
        }

        return CourseOffering::query()
            ->where('id', $this->input('course_offering_id'))
            ->where('lecturer_id', $lecturer->id)
            ->exists();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = [
            'course_offering_id' => ['required', 'integer', 'exists:course_offerings,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date', 'after:now'],
            'max_score' => ['required', 'numeric', 'min:1', 'max:1000'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['course_offering_id'] = ['sometimes', 'integer', 'exists:course_offerings,id'];
            $rules['due_date'] = ['nullable', 'date'];
        }

        return $rules;
    }

    public function toAssignmentData(): \App\DTO\AssignmentData
    {
        return \App\DTO\AssignmentData::from([
            'course_offering_id' => (int) $this->input('course_offering_id', 0),
            'title' => (string) $this->input('title'),
            'description' => $this->input('description'),
            'due_date' => $this->input('due_date'),
            'max_score' => (float) $this->input('max_score', 100),
        ]);
    }
}
