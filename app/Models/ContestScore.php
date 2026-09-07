<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContestScore extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['score' => 'decimal:2']; }
    public function application(): BelongsTo { return $this->belongsTo(ContestApplication::class, 'contest_application_id'); }
    public function subject(): BelongsTo { return $this->belongsTo(ContestSubject::class, 'contest_subject_id'); }
}
