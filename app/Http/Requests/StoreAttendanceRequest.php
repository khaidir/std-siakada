<?php

namespace App\Http\Requests;

use App\DTO\BatchAttendanceData;
use App\Enums\AttendanceStatus;
use App\Models\CourseOffering;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Hanya dosen pengampu kelas yang boleh mengisi presensi.
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
            'meeting_number' => ['required', 'integer', 'min:1'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.student_id' => ['required', 'integer', 'exists:students,id'],
            'attendances.*.status' => ['required', Rule::enum(AttendanceStatus::class)],
        ];
    }

    public function toBatchAttendanceData(): BatchAttendanceData
    {
        $offeringId = (int) $this->integer('course_offering_id');
        $meetingNumber = (int) $this->integer('meeting_number');
        $date = (string) $this->input('date');

        $attendances = collect($this->validated('attendances'))
            ->map(fn (array $row) => [
                ...$row,
                'course_offering_id' => $offeringId,
                'meeting_number' => $meetingNumber,
                'date' => $date,
                'student_id' => (int) $row['student_id'],
            ])
            ->all();

        return BatchAttendanceData::from(['attendances' => $attendances]);
    }
}
