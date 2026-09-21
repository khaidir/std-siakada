<?php

namespace App\Services;

use App\DTO\AssignmentData;
use App\DTO\SubmissionGradeData;
use App\Models\Assignment;
use App\Models\User;
use App\Repositories\Contracts\AssignmentRepository;
use App\Repositories\Contracts\CourseOfferingRepository;
use App\Repositories\Contracts\SubmissionRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignmentService
{
    public function __construct(
        private readonly AssignmentRepository $assignments,
        private readonly SubmissionRepository $submissions,
        private readonly CourseOfferingRepository $offerings,
    ) {}

    /**
     * Data halaman tugas dosen: daftar kelas diampu + tugas kelas terpilih.
     *
     * @return array<string, mixed>
     */
    public function pageData(User $user, ?int $offeringId = null): array
    {
        $lecturerId = (int) $user->lecturer?->id;

        $offerings = $this->offerings->listByLecturer($lecturerId);

        $offeringOptions = $offerings
            ->map(fn ($o) => [
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
            'assignments' => $selected > 0 ? $this->assignmentList($selected) : [],
            'selected_offering_id' => $selected,
        ];
    }

    /**
     * Data submission untuk penilaian.
     *
     * @return array<string, mixed>
     */
    public function submissionData(User $user, int $assignmentId): array
    {
        $assignment = $this->assignments->findById($assignmentId);

        if (! $assignment) {
            throw ValidationException::withMessages([
                'assignment' => 'Tugas tidak ditemukan.',
            ]);
        }

        // Pastikan dosen pengampu kelas ini
        $offering = $this->offerings->findById($assignment->course_offering_id);
        $lecturerId = (int) $user->lecturer?->id;

        if (! $offering || (int) $offering->lecturer_id !== $lecturerId) {
            abort(403, 'Anda tidak mengajar kelas ini.');
        }

        $submissions = $this->submissions->listByAssignment($assignmentId);

        return [
            'assignment' => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'max_score' => $assignment->max_score,
            ],
            'submissions' => $submissions->map(fn ($s) => [
                'id' => $s->id,
                'student_id' => $s->student_id,
                'nim' => $s->student?->nim,
                'student_name' => $s->student?->user?->name,
                'file_path' => $s->file_path,
                'file_url' => $s->file_path ? asset('storage/' . $s->file_path) : null,
                'submitted_at' => $s->submitted_at?->format('Y-m-d H:i'),
                'score' => $s->score,
                'feedback' => $s->feedback,
            ]),
        ];
    }

    /**
     * Simpan tugas baru.
     */
    public function store(User $user, AssignmentData $data): void
    {
        // Pastikan dosen mengajar kelas ini
        $offering = $this->offerings->findById($data->course_offering_id);
        $lecturerId = (int) $user->lecturer?->id;

        if (! $offering || (int) $offering->lecturer_id !== $lecturerId) {
            abort(403, 'Anda tidak mengajar kelas ini.');
        }

        $this->assignments->create([
            'course_offering_id' => $data->course_offering_id,
            'title' => $data->title,
            'description' => $data->description,
            'due_date' => $data->due_date,
            'max_score' => $data->max_score,
        ]);
    }

    /**
     * Update tugas.
     */
    public function update(User $user, int $id, AssignmentData $data): void
    {
        $assignment = $this->assignments->findById($id);

        if (! $assignment) {
            throw ValidationException::withMessages([
                'assignment' => 'Tugas tidak ditemukan.',
            ]);
        }

        // Pastikan dosen pengampu kelas ini
        $offering = $this->offerings->findById($assignment->course_offering_id);
        $lecturerId = (int) $user->lecturer?->id;

        if (! $offering || (int) $offering->lecturer_id !== $lecturerId) {
            abort(403, 'Anda tidak mengajar kelas ini.');
        }

        $this->assignments->update($id, [
            'title' => $data->title,
            'description' => $data->description,
            'due_date' => $data->due_date,
            'max_score' => $data->max_score,
        ]);
    }

    /**
     * Hapus tugas.
     */
    public function destroy(User $user, int $id): void
    {
        $assignment = $this->assignments->findById($id);

        if (! $assignment) {
            throw ValidationException::withMessages([
                'assignment' => 'Tugas tidak ditemukan.',
            ]);
        }

        // Pastikan dosen pengampu kelas ini
        $offering = $this->offerings->findById($assignment->course_offering_id);
        $lecturerId = (int) $user->lecturer?->id;

        if (! $offering || (int) $offering->lecturer_id !== $lecturerId) {
            abort(403, 'Anda tidak mengajar kelas ini.');
        }

        $this->assignments->delete($id);
    }

    /**
     * Nilai submission secara batch.
     */
    public function gradeSubmissions(User $user, int $assignmentId, array $rows): void
    {
        $assignment = $this->assignments->findById($assignmentId);

        if (! $assignment) {
            throw ValidationException::withMessages([
                'assignment' => 'Tugas tidak ditemukan.',
            ]);
        }

        // Pastikan dosen pengampu kelas ini
        $offering = $this->offerings->findById($assignment->course_offering_id);
        $lecturerId = (int) $user->lecturer?->id;

        if (! $offering || (int) $offering->lecturer_id !== $lecturerId) {
            abort(403, 'Anda tidak mengajar kelas ini.');
        }

        $maxScore = $assignment->max_score;

        DB::transaction(function () use ($rows, $maxScore) {
            $updateRows = [];

            foreach ($rows as $row) {
                $score = (float) ($row['score'] ?? 0);

                if ($score < 0 || $score > $maxScore) {
                    throw ValidationException::withMessages([
                        'score' => "Skor harus antara 0 dan {$maxScore}.",
                    ]);
                }

                $updateRows[] = [
                    'submission_id' => (int) $row['submission_id'],
                    'score' => $score,
                    'feedback' => $row['feedback'] ?? null,
                ];
            }

            $this->submissions->gradeBatch($updateRows);
        });
    }

    /**
     * Daftar tugas untuk suatu offering.
     */
    private function assignmentList(int $offeringId): Collection
    {
        return $this->assignments->listByOffering($offeringId)
            ->map(fn (Assignment $a) => [
                'id' => $a->id,
                'title' => $a->title,
                'description' => $a->description,
                'due_date' => $a->due_date?->format('Y-m-d H:i'),
                'max_score' => $a->max_score,
                'submissions_count' => (int) $a->submissions_count,
            ]);
    }
}
