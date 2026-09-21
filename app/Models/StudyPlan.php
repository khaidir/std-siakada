<?php

namespace App\Models;

use App\Enums\StudyPlanStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['student_id', 'semester_id', 'status', 'approved_by', 'approved_at', 'notes'])]
class StudyPlan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => StudyPlanStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function studyPlanDetails(): HasMany
    {
        return $this->hasMany(StudyPlanDetail::class);
    }
}
