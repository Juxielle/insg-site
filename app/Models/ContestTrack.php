<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContestTrack extends Model
{
    protected $guarded = [];
    public function contest(): BelongsTo { return $this->belongsTo(Contest::class); }
    public function subjects(): HasMany { return $this->hasMany(ContestSubject::class)->orderBy('sort_order'); }
    public function applications(): HasMany { return $this->hasMany(ContestApplication::class); }
}
