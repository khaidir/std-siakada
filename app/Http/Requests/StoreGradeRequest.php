<?php

namespace App\Http\Requests;

use App\DTO\BatchGradeData;
use App\Models\CourseOffering;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreGradeRequest extends FormRequest
{
    /**
     * Hanya dosen pengampu kelas yang boleh menyimpan nilai.
     */
    public function authorize(): bool
    {
        $lecturer = $this->user()?->lecturer;

        if (! $lecturer) {
            return false;
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
        return [
            'course_offering_id' => ['required', 'integer', 'exists:course_offerings,id'],
            'grades' => ['required', 'array', 'min:1'],
            'grades.*.study_plan_detail_id' => ['required', 'integer', 'exists:study_plan_details,id'],
            'grades.*.student_id' => ['required', 'integer', 'exists:students,id'],
            'grades.*.assignment_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.midterm_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.final_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.score' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /**
     * Validasi lanjutan: setiap baris harus punya komponen atau skor akhir.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                foreach ($this->input('grades', []) as $index => $row) {
                    $assignment = $row['assignment_score'] ?? null;
                    $midterm = $row['midterm_score'] ?? null;
                    $final = $row['final_score'] ?? null;
                    $score = $row['score'] ?? null;

                    $hasComponents = $this->hasValue($assignment)
                        && $this->hasValue($midterm)
                        && $this->hasValue($final);
                    $hasScore = $this->hasValue($score);

                    if (! $hasComponents && ! $hasScore) {
                        $validator->errors()->add(
                            "grades.$index.score",
                            'Isi nilai tugas, UTS, UAS, atau skor akhir.',
                        );
                    }
                }
            },
        ];
    }

    public function toBatchGradeData(): BatchGradeData
    {
        $offeringId = (int) $this->integer('course_offering_id');

        $grades = collect($this->validated('grades'))
            ->map(fn (array $row) => [
                ...$row,
                'course_offering_id' => $offeringId,
                'study_plan_detail_id' => (int) $row['study_plan_detail_id'],
                'student_id' => (int) $row['student_id'],
            ])
            ->all();

        return BatchGradeData::from(['grades' => $grades]);
    }

    private function hasValue(mixed $value): bool
    {
        return $value !== null && $value !== '';
    }
}
