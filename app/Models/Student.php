<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id',
    'nim',
    'study_program_id',
    'entry_year',
    'status',
    'gpa',
    'total_sks',
    // Biodata yang boleh diubah mahasiswa lewat halaman Profile Settings.
    'birth_place',
    'birth_date',
    'gender',
    'address',
    'phone',
])]
class Student extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => StudentStatus::class,
            'gpa' => 'decimal:2',
            'total_sks' => 'integer',
            'birth_date' => 'date',
            'gender' => Gender::class,
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

    public function studyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function thesis(): HasOne
    {
        return $this->hasOne(Thesis::class);
    }

    public function internship(): HasOne
    {
        return $this->hasOne(Internship::class);
    }
}
