<?php

namespace App\Models;

use App\Enums\CourseType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['study_program_id', 'code', 'name', 'sks', 'semester', 'type'])]
class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'sks' => 'integer',
            'semester' => 'integer',
            'type' => CourseType::class,
        ];
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }
}
