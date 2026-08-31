<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContestResult extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['average' => 'decimal:2']; }
    public function application(): BelongsTo { return $this->belongsTo(ContestApplication::class, 'contest_application_id'); }
}
