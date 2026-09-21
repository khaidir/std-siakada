<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'target_role' => ['nullable', 'string', Rule::in(['super-admin', 'kaprodi', 'dosen', 'mahasiswa', 'pimpinan'])],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'content.required' => 'Konten pengumuman wajib diisi.',
            'target_role.in' => 'Target role tidak valid.',
        ];
    }
}
