<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Student;
use App\Models\StudyPlanDetail;
use App\Repositories\Contracts\AssignmentRepository;
use App\Repositories\Contracts\SubmissionRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SubmissionService
{
    public function __construct(
        private readonly AssignmentRepository $assignments,
        private readonly SubmissionRepository $submissions,
    ) {}

    /**
     * Data halaman tugas mahasiswa.
     *
     * @return array<string, mixed>
     */
    public function pageData(int $studentId): array
    {
        $assignments = $this->assignments->listForStudent($studentId);

        $items = $assignments->map(function (Assignment $assignment) {
            $submission = $assignment->assignmentSubmissions->first();

            return [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'due_date' => $assignment->due_date?->toDateTimeString(),
                'max_score' => (float) ($assignment->max_score ?? 0),
                'course_name' => $assignment->courseOffering?->course?->name ?? '',
                'course_code' => $assignment->courseOffering?->course?->code ?? '',
                'is_past_due' => $assignment->due_date ? $assignment->due_date->isPast() : false,
                'submission' => $submission ? [
                    'id' => $submission->id,
                    'file_path' => $submission->file_path,
                    'file_url' => $submission->file_path ? Storage::url($submission->file_path) : null,
                    'submitted_at' => $submission->submitted_at?->toDateTimeString(),
                    'score' => $submission->score,
                    'feedback' => $submission->feedback,
                ] : null,
            ];
        })->values()->all();

        return [
            'assignments' => $items,
        ];
    }

    /**
     * Submit tugas.
     */
    public function submit(int $studentId, int $assignmentId, UploadedFile $file): void
    {
        DB::transaction(function () use ($studentId, $assignmentId, $file) {
            // Cek mahasiswa terdaftar di kelas (via study_plan_detail approved).
            $isEnrolled = StudyPlanDetail::query()
                ->whereHas('studyPlan', fn ($q) => $q->where('student_id', $studentId))
                ->whereHas('courseOffering.assignments', fn ($q) => $q->where('id', $assignmentId))
                ->where('status', 'approved')
                ->exists();

            if (! $isEnrolled) {
                throw ValidationException::withMessages([
                    'assignment_id' => 'Anda tidak terdaftar di kelas tugas ini.',
                ]);
            }

            // Cek due_date belum lewat.
            $assignment = Assignment::query()
                ->select(['id', 'due_date'])
                ->where('id', $assignmentId)
                ->firstOrFail();

            if ($assignment->due_date && $assignment->due_date->isPast()) {
                throw ValidationException::withMessages([
                    'assignment_id' => 'Tenggat pengumpulan tugas sudah lewat.',
                ]);
            }

            // Simpan file.
            $path = $file->store('submissions/' . $assignmentId, 'public');

            // Cek apakah sudah pernah submit — update atau create.
            $existing = $this->submissions->findByAssignmentAndStudent($assignmentId, $studentId);

            if ($existing) {
                // Hapus file lama.
                if ($existing->file_path) {
                    Storage::disk('public')->delete($existing->file_path);
                }

                $this->submissions->update($existing->id, [
                    'file_path' => $path,
                    'submitted_at' => now(),
                ]);
            } else {
                $this->submissions->create([
                    'assignment_id' => $assignmentId,
                    'student_id' => $studentId,
                    'file_path' => $path,
                    'submitted_at' => now(),
                ]);
            }
        });
    }
}
