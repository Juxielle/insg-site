<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['continuous_assessment' => 'decimal:2', 'exam' => 'decimal:2'];
    }

    protected function average(): Attribute
    {
        return Attribute::get(fn () => round(($this->continuous_assessment + $this->exam) / 2, 2));
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
