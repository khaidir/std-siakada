<?php

namespace App\Repositories\Eloquent;

use App\Enums\ThesisStatus;
use App\Models\Thesis;
use App\Models\ThesisLog;
use App\Repositories\Contracts\ThesisRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentThesisRepository implements ThesisRepository
{
    public function findForStudent(int $studentId): ?Thesis
    {
        return Thesis::query()
            ->select(['id', 'student_id', 'title', 'abstract', 'supervisor_1_id', 'supervisor_2_id', 'status', 'submission_date'])
            ->with([
                'supervisorOne:id,user_id',
                'supervisorOne.user:id,name',
                'supervisorTwo:id,user_id',
                'supervisorTwo.user:id,name',
            ])
            ->where('student_id', $studentId)
            ->first();
    }

    public function listForSupervisor(int $lecturerId): Collection
    {
        return Thesis::query()
            ->select(['id', 'student_id', 'title', 'status', 'submission_date', 'supervisor_1_id', 'supervisor_2_id'])
            ->where(function ($q) use ($lecturerId) {
                $q->where('supervisor_1_id', $lecturerId)
                    ->orWhere('supervisor_2_id', $lecturerId);
            })
            ->with([
                'student:id,name,nim,study_program_id',
                'student.studyProgram:id,name',
            ])
            ->latest()
            ->get();
    }

    public function detail(int $thesisId): ?Thesis
    {
        return Thesis::query()
            ->select(['id', 'student_id', 'title', 'abstract', 'supervisor_1_id', 'supervisor_2_id', 'status', 'submission_date'])
            ->where('id', $thesisId)
            ->with([
                'student:id,name,nim,study_program_id',
                'student.studyProgram:id,name',
                'supervisorOne:id,user_id',
                'supervisorOne.user:id,name',
                'supervisorTwo:id,user_id',
                'supervisorTwo.user:id,name',
            ])
            ->first();
    }

    public function listLogs(int $thesisId): Collection
    {
        return ThesisLog::query()
            ->select(['id', 'thesis_id', 'date', 'activity', 'notes', 'supervisor_approval', 'created_at'])
            ->where('thesis_id', $thesisId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function addLog(array $data): ThesisLog
    {
        return ThesisLog::query()->create([
            'thesis_id' => $data['thesis_id'],
            'date' => $data['date'],
            'activity' => $data['activity'],
            'notes' => $data['notes'] ?? null,
            'supervisor_approval' => false,
        ]);
    }

    public function updateStatus(int $thesisId, ThesisStatus $status): void
    {
        Thesis::query()
            ->where('id', $thesisId)
            ->update(['status' => $status->value]);
    }

    public function approveLog(int $logId): void
    {
        ThesisLog::query()
            ->where('id', $logId)
            ->update(['supervisor_approval' => true]);
    }

    public function listAll(array $filters): LengthAwarePaginator
    {
        return Thesis::query()
            ->select(['id', 'student_id', 'title', 'supervisor_1_id', 'supervisor_2_id', 'status', 'submission_date'])
            ->with([
                'student:id,name,nim,study_program_id',
                'student.studyProgram:id,name',
                'supervisorOne:id,user_id',
                'supervisorOne.user:id,name',
                'supervisorTwo:id,user_id',
                'supervisorTwo.user:id,name',
            ])
            ->when($filters['study_program_id'] ?? null, function ($q, $studyProgramId) {
                $q->whereHas('student', fn($q) => $q->where('study_program_id', $studyProgramId));
            })
            ->when($filters['status'] ?? null, function ($q, $status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate(15);
    }

    public function assignSupervisors(int $thesisId, int $supervisor1Id, ?int $supervisor2Id): void
    {
        Thesis::query()
            ->where('id', $thesisId)
            ->update([
                'supervisor_1_id' => $supervisor1Id,
                'supervisor_2_id' => $supervisor2Id,
            ]);
    }
}
