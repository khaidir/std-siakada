<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LecturerAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_offering_id' => ['required', 'integer', 'exists:course_offerings,id'],
            'date' => ['required', 'date'],
            'check_in' => ['nullable', 'date_format:H:i:s'],
        ];
    }

    public function messages(): array
    {
        return [
            'course_offering_id.required' => 'Kelas harus diisi.',
            'course_offering_id.exists' => 'Kelas tidak ditemukan.',
            'date.required' => 'Tanggal harus diisi.',
            'date.date' => 'Format tanggal tidak valid.',
            'check_in.date_format' => 'Format jam tidak valid (HH:MM:SS).',
        ];
    }
}
