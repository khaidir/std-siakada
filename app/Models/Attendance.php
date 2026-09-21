<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['course_offering_id', 'student_id', 'meeting_number', 'date', 'status'])]
class Attendance extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'meeting_number' => 'integer',
            'date' => 'date',
            'status' => AttendanceStatus::class,
        ];
    }

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
