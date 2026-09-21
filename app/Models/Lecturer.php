<?php

namespace App\Models;

use App\Enums\AcademicRank;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'nidn', 'study_program_id', 'academic_rank'])]
class Lecturer extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'academic_rank' => AcademicRank::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }

    public function lecturerAttendances(): HasMany
    {
        return $this->hasMany(LecturerAttendance::class);
    }

    public function supervisedTheses(): HasMany
    {
        return $this->hasMany(Thesis::class, 'supervisor_1_id');
    }

    public function coSupervisedTheses(): HasMany
    {
        return $this->hasMany(Thesis::class, 'supervisor_2_id');
    }

    public function supervisedInternships(): HasMany
    {
        return $this->hasMany(Internship::class, 'supervisor_id');
    }
}
