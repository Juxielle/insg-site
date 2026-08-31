<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contest extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['registration_starts_at' => 'datetime', 'registration_ends_at' => 'datetime', 'exam_date' => 'date', 'results_validated_at' => 'datetime', 'published_at' => 'datetime', 'closed_at' => 'datetime'];
    }

    public function applications(): HasMany { return $this->hasMany(ContestApplication::class); }
    public function isRegistrationOpen(): bool { return $this->status === 'registration_open' && now()->between($this->registration_starts_at, $this->registration_ends_at); }
    public function resultsArePublic(): bool { return $this->status === 'results_published' && $this->published_at !== null; }
}
