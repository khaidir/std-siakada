<?php

namespace App\Services;

use App\DTO\BatchGradeData;
use App\DTO\GradeData;
use App\Models\CourseOffering;
use App\Models\Grade;
use App\Models\StudyPlanDetail;
use App\Models\User;
use App\Repositories\Contracts\CourseOfferingRepository;
use App\Repositories\Contracts\GradeRepository;
use App\Repositories\Contracts\StudentRepository;
use App\Repositories\Contracts\StudyPlanDetailRepository;
use App\Support\GradeCalculator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GradeService
{
    public function __construct(
        private readonly GradeRepository $grades,
        private readonly StudyPlanDetailRepository $details,
        private readonly StudentRepository $students,
        private readonly CourseOfferingRepository $offerings,
    ) {}

    /**
     * Data halaman input nilai: daftar kelas diampu + mahasiswa kelas terpilih.
     *
     * @return array<string, mixed>
     */
    public function pageData(User $user, ?int $offeringId = null): array
    {
        $lecturerId = (int) $user->lecturer?->id;

        $offerings = $this->offerings->listByLecturer($lecturerId);

        $offeringOptions = $offerings
            ->map(fn (CourseOffering $o) => [
                'id' => $o->id,
                'label' => trim(sprintf(
                    '%s (%s) · %s · %s',
                    $o->course?->name,
                    $o->course?->code,
                    $o->semester?->type?->value ?? '',
                    $o->day?->value ?? '',
                )),
            ])
            ->values()
            ->all();

        $selected = $offeringId ?? (int) ($offerings->first()?->id ?? 0);

        return [
            'offerings' => $offeringOptions,
            'students' => $selected > 0 ? $this->studentsData($selected) : [],
            'selected_offering_id' => $selected,
        ];
    }

    /**
     * Simpan nilai batch satu kelas dalam satu transaksi.
     */
    public function store(int $offeringId, BatchGradeData $data): void
    {
        DB::transaction(function () use ($offeringId, $data) {
            $detailIds = array_values(array_unique(
                collect($data->grades)->pluck('study_plan_detail_id')->all(),
            ));

            $validDetails = $this->details->approvedForOffering($offeringId, $detailIds);
            $validIds = $validDetails->pluck('id')->all();
            $studentByDetail = $validDetails
                ->mapWithKeys(fn (StudyPlanDetail $d) => [$d->id => (int) $d->studyPlan?->student_id])
                ->all();

            $rows = [];
            $studentIds = [];

            /** @var GradeData $grade */
            foreach ($data->grades as $grade) {
                if (! in_array($grade->study_plan_detail_id, $validIds, true)) {
                    throw ValidationException::withMessages([
                        'grades' => 'Terdapat detail KRS yang tidak valid untuk kelas ini.',
                    ]);
                }

                $studentId = $studentByDetail[$grade->study_plan_detail_id];

                $score = GradeCalculator::final(
                    $grade->assignment_score,
                    $grade->midterm_score,
                    $grade->final_score,
                ) ?? $grade->score;

                if ($score === null) {
                    throw ValidationException::withMessages([
                        'grades' => 'Lengkapi komponen nilai (tugas/UTS/UAS) atau skor akhir.',
                    ]);
                }

                [$letter, $point] = GradeCalculator::letterAndPoint((float) $score);

                $rows[] = [
                    'study_plan_detail_id' => $grade->study_plan_detail_id,
                    'student_id' => $studentId,
                    'course_offering_id' => $offeringId,
                    'assignment_score' => $grade->assignment_score,
                    'midterm_score' => $grade->midterm_score,
                    'final_score' => $grade->final_score,
                    'score' => round((float) $score, 2),
                    'letter_grade' => $letter->value,
                    'grade_point' => round($point, 2),
                ];

                $studentIds[] = $studentId;
            }

            $this->grades->upsertBatch($rows);

            foreach (array_values(array_unique($studentIds)) as $studentId) {
                $this->students->recalculateGpaAndSks((int) $studentId);
            }
        });
    }

    /**
     * Ringkasan nilai untuk mahasiswa: daftar nilai + IPK + total SKS.
     *
     * @return array<string, mixed>
     */
    public function summaryForStudent(int $studentId): array
    {
        $grades = $this->grades->listForStudent($studentId);

        $items = $grades->map(function (Grade $grade) {
            $course = $grade->studyPlanDetail?->courseOffering?->course;

            return [
                'id' => $grade->id,
                'course_code' => $course?->code ?? '',
                'course_name' => $course?->name ?? '',
                'sks' => (int) ($course?->sks ?? 0),
                'score' => (float) ($grade->score ?? 0),
                'letter_grade' => $grade->letter_grade?->value ?? '',
                'grade_point' => (float) ($grade->grade_point ?? 0),
            ];
        })->values()->all();

        $totalSks = 0;
        $totalPoints = 0.0;

        foreach ($grades as $grade) {
            $sks = (int) ($grade->studyPlanDetail?->courseOffering?->course?->sks ?? 0);

            if ($sks <= 0) {
                continue;
            }

            $totalSks += $sks;
            $totalPoints += (float) ($grade->grade_point ?? 0) * $sks;
        }

        $gpa = $totalSks > 0 ? round($totalPoints / $totalSks, 2) : 0.0;

        return [
            'grades' => $items,
            'gpa' => $gpa,
            'total_sks' => $totalSks,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function studentsData(int $offeringId): array
    {
        return $this->details->listApprovedByOffering($offeringId)
            ->map(fn (StudyPlanDetail $detail) => [
                'study_plan_detail_id' => $detail->id,
                'student_id' => (int) ($detail->studyPlan?->student_id ?? 0),
                'nim' => $detail->studyPlan?->student?->nim,
                'name' => $detail->studyPlan?->student?->user?->name,
                'assignment_score' => $this->num($detail->grade?->assignment_score),
                'midterm_score' => $this->num($detail->grade?->midterm_score),
                'final_score' => $this->num($detail->grade?->final_score),
                'score' => $this->num($detail->grade?->score),
                'letter_grade' => $detail->grade?->letter_grade?->value,
                'grade_point' => $this->num($detail->grade?->grade_point),
            ])
            ->all();
    }

    private function num(mixed $value): ?float
    {
        return ($value === null || $value === '') ? null : (float) $value;
    }
}
