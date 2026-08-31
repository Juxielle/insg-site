<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContestApplication extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['documents' => 'array', 'submitted_at' => 'datetime', 'reviewed_at' => 'datetime']; }
    public function contest(): BelongsTo { return $this->belongsTo(Contest::class); }
    public function candidate(): BelongsTo { return $this->belongsTo(Candidate::class); }
    public function result(): HasOne { return $this->hasOne(ContestResult::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
}
