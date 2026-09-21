<?php

namespace App\Models;

use App\Enums\StudyPlanDetailStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['study_plan_id', 'course_offering_id', 'status'])]
class StudyPlanDetail extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => StudyPlanDetailStatus::class,
        ];
    }

    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class);
    }

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function grade(): HasOne
    {
        return $this->hasOne(Grade::class);
    }
}
