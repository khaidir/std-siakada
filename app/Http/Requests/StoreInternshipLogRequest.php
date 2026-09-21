<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInternshipLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'internship_id' => ['required', 'integer', 'exists:internships,id'],
            'date' => ['required', 'date'],
            'activity' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'internship_id.required' => 'KP wajib dipilih.',
            'internship_id.exists' => 'KP tidak ditemukan.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Tanggal tidak valid.',
            'activity.required' => 'Aktivitas wajib diisi.',
            'activity.max' => 'Aktivitas maksimal 255 karakter.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
