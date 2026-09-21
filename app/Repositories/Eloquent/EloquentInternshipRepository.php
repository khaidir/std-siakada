<?php

namespace App\Repositories\Eloquent;

use App\Enums\InternshipStatus;
use App\Models\Internship;
use App\Models\InternshipLog;
use App\Repositories\Contracts\InternshipRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentInternshipRepository implements InternshipRepository
{
    public function findForStudent(int $studentId): ?Internship
    {
        return Internship::query()
            ->select(['id', 'student_id', 'company_name', 'address', 'supervisor_id', 'field_supervisor', 'start_date', 'end_date', 'status'])
            ->with([
                'supervisor:id,user_id',
                'supervisor.user:id,name',
            ])
            ->where('student_id', $studentId)
            ->first();
    }

    public function listLogs(int $internshipId): Collection
    {
        return InternshipLog::query()
            ->select(['id', 'internship_id', 'date', 'activity', 'notes', 'approval'])
            ->where('internship_id', $internshipId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function addLog(array $data): InternshipLog
    {
        return InternshipLog::query()->create([
            'internship_id' => $data['internship_id'],
            'date' => $data['date'],
            'activity' => $data['activity'],
            'notes' => $data['notes'] ?? null,
            'approval' => \App\Enums\InternshipLogApproval::Pending,
        ]);
    }

    public function listAll(array $filters): LengthAwarePaginator
    {
        return Internship::query()
            ->select(['id', 'student_id', 'company_name', 'supervisor_id', 'field_supervisor', 'start_date', 'end_date', 'status'])
            ->with([
                'student:id,name,nim,study_program_id',
                'student.studyProgram:id,name',
                'supervisor:id,user_id',
                'supervisor.user:id,name',
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

    public function detail(int $internshipId): ?Internship
    {
        return Internship::query()
            ->select(['id', 'student_id', 'company_name', 'address', 'supervisor_id', 'field_supervisor', 'start_date', 'end_date', 'status'])
            ->where('id', $internshipId)
            ->with([
                'student:id,name,nim,study_program_id',
                'student.studyProgram:id,name',
                'supervisor:id,user_id',
                'supervisor.user:id,name',
            ])
            ->first();
    }

    public function assignSupervisor(int $internshipId, int $lecturerId): void
    {
        Internship::query()
            ->where('id', $internshipId)
            ->update(['supervisor_id' => $lecturerId]);
    }

    public function updateStatus(int $internshipId, InternshipStatus $status): void
    {
        Internship::query()
            ->where('id', $internshipId)
            ->update(['status' => $status->value]);
    }
}
