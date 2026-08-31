<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['birth_date' => 'date']; }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function applications(): HasMany { return $this->hasMany(ContestApplication::class); }
}
