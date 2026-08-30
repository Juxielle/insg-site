<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = ['type', 'name', 'email', 'phone', 'data', 'documents'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'documents' => 'array',
        ];
    }
}
