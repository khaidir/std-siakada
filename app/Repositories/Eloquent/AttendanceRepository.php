<?php

namespace App\Repositories\Eloquent;

use App\Models\Attendance;
use App\Repositories\Contracts\AttendanceRepository as AttendanceRepositoryContract;
use Illuminate\Support\Collection;

class AttendanceRepository implements AttendanceRepositoryContract
{
    public function listByOffering(int $offeringId): Collection
    {
        return Attendance::query()
            ->select(['id', 'course_offering_id', 'student_id', 'meeting_number', 'date', 'status'])
            ->with('student:id,user_id,nim', 'student.user:id,name')
            ->where('course_offering_id', $offeringId)
            ->orderBy('date')
            ->orderBy('meeting_number')
            ->get();
    }

    public function recapByOffering(int $offeringId): Collection
    {
        return Attendance::query()
            ->select(['meeting_number', 'date', 'status'])
            ->where('course_offering_id', $offeringId)
            ->orderBy('date')
            ->orderBy('meeting_number')
            ->get();
    }

    public function listForStudent(int $studentId, int $offeringId): Collection
    {
        return Attendance::query()
            ->select(['id', 'course_offering_id', 'meeting_number', 'date', 'status'])
            ->where('student_id', $studentId)
            ->where('course_offering_id', $offeringId)
            ->orderBy('meeting_number')
            ->get();
    }

    public function upsertBatch(array $rows): void
    {
        foreach ($rows as $row) {
            Attendance::updateOrCreate(
                [
                    'course_offering_id' => $row['course_offering_id'],
                    'student_id' => $row['student_id'],
                    'meeting_number' => $row['meeting_number'],
                    'date' => $row['date'],
                ],
                ['status' => $row['status']],
            );
        }
    }
}
