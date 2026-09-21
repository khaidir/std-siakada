<?php

namespace App\Http\Requests;

use App\Models\CourseOffering;
use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
{
    /**
     * Hanya dosen pengampu kelas yang boleh mengelola materi.
     */
    public function authorize(): bool
    {
        $lecturer = $this->user()?->lecturer;

        if (! $lecturer) {
            return false;
        }

        // Untuk update, cek via route {id} — cari materi lalu cek kepemilikan kelas.
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $material = \App\Models\CourseMaterial::query()->findOrFail(
                (int) $this->route('id'),
            );

            return $material->courseOffering?->lecturer_id === $lecturer->id;
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
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,jpg,jpeg,png,mp4,mp3', 'max:20480'],
        ];

        // Untuk update, course_offering_id tidak wajib (bisa dari route/query).
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['course_offering_id'] = ['sometimes', 'integer', 'exists:course_offerings,id'];
        }

        return $rules;
    }

    public function toMaterialData(): \App\DTO\MaterialData
    {
        return \App\DTO\MaterialData::from([
            'course_offering_id' => (int) $this->input('course_offering_id', 0),
            'title' => (string) $this->input('title'),
            'description' => $this->input('description'),
            'uploaded_by' => (int) $this->user()?->id,
        ]);
    }
}
