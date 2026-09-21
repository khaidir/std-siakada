<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseOfferingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'lecturer_id' => ['required', 'integer', 'exists:lecturers,id'],
            'classroom_id' => ['required', 'integer', 'exists:classrooms,id'],
            'day' => ['required', 'string', Rule::in(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'])],
            'start_time' => ['required', 'string', 'date_format:H:i'],
            'end_time' => ['required', 'string', 'date_format:H:i', 'after:start_time'],
            'quota' => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.required' => 'Mata kuliah wajib dipilih.',
            'course_id.exists' => 'Mata kuliah tidak valid.',
            'semester_id.required' => 'Semester wajib dipilih.',
            'semester_id.exists' => 'Semester tidak valid.',
            'lecturer_id.required' => 'Dosen wajib dipilih.',
            'lecturer_id.exists' => 'Dosen tidak valid.',
            'classroom_id.required' => 'Ruangan wajib dipilih.',
            'classroom_id.exists' => 'Ruangan tidak valid.',
            'day.required' => 'Hari wajib dipilih.',
            'day.in' => 'Hari tidak valid.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'start_time.date_format' => 'Format jam mulai tidak valid (HH:mm).',
            'end_time.required' => 'Jam selesai wajib diisi.',
            'end_time.date_format' => 'Format jam selesai tidak valid (HH:mm).',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
            'quota.required' => 'Kuota wajib diisi.',
            'quota.min' => 'Kuota minimal 1.',
        ];
    }
}
