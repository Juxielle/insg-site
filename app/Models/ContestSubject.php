<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContestSubject extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['max_score' => 'decimal:2']; }
    public function track(): BelongsTo { return $this->belongsTo(ContestTrack::class, 'contest_track_id'); }
    public function scores(): HasMany { return $this->hasMany(ContestScore::class); }
}
