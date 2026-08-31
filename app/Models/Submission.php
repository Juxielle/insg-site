<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    protected $fillable = ['type', 'name', 'email', 'phone', 'data', 'documents', 'status', 'admin_note', 'reviewed_by', 'reviewed_at'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'documents' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
