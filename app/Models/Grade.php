<?php

namespace App\Models;

use App\Enums\GradeLetter;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['study_plan_detail_id', 'student_id', 'course_offering_id', 'assignment_score', 'midterm_score', 'final_score', 'score', 'letter_grade', 'grade_point'])]
class Grade extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'assignment_score' => 'decimal:2',
            'midterm_score' => 'decimal:2',
            'final_score' => 'decimal:2',
            'score' => 'decimal:2',
            'grade_point' => 'decimal:2',
            'letter_grade' => GradeLetter::class,
        ];
    }

    public function studyPlanDetail(): BelongsTo
    {
        return $this->belongsTo(StudyPlanDetail::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }
}
