<?php

namespace App\Models;

use App\Enums\DegreeLevel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['faculty_id', 'code', 'name', 'degree_level', 'head_id'])]
class StudyProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'degree_level' => DegreeLevel::class,
        ];
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function lecturers(): HasMany
    {
        return $this->hasMany(Lecturer::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
