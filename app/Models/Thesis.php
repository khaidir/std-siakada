<?php

namespace App\Models;

use App\Enums\ThesisStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['student_id', 'title', 'abstract', 'supervisor_1_id', 'supervisor_2_id', 'status', 'submission_date'])]
class Thesis extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => ThesisStatus::class,
            'submission_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function supervisorOne(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'supervisor_1_id');
    }

    public function supervisorTwo(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'supervisor_2_id');
    }

    public function thesisLogs(): HasMany
    {
        return $this->hasMany(ThesisLog::class);
    }
}
