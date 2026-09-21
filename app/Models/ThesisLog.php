<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['thesis_id', 'date', 'activity', 'notes', 'supervisor_approval'])]
class ThesisLog extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'supervisor_approval' => 'boolean',
        ];
    }

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }
}
