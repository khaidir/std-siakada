<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Otorisasi dilakukan di Policy/Service.
    }

    public function rules(): array
    {
        return [
            'assignment_id' => ['required', 'integer', 'exists:assignments,id'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File tugas wajib diunggah.',
            'file.mimes' => 'File harus berupa PDF, dokumen, spreadsheet, presentasi, arsip (ZIP/RAR), atau gambar.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ];
    }
}
