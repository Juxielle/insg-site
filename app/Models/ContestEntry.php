<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContestEntry extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['average' => 'decimal:2', 'birth_date' => 'date']; }
    public function contest(): BelongsTo { return $this->belongsTo(Contest::class); }
}
