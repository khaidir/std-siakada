<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreThesisLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'thesis_id' => ['required', 'integer', 'exists:theses,id'],
            'date' => ['required', 'date'],
            'activity' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'thesis_id.required' => 'Skripsi wajib dipilih.',
            'thesis_id.exists' => 'Skripsi tidak ditemukan.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Tanggal tidak valid.',
            'activity.required' => 'Aktivitas wajib diisi.',
            'activity.max' => 'Aktivitas maksimal 255 karakter.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
