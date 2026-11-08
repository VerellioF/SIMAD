<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CharacterRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'category',
        'points',
        'description',
        'is_active',
    ];

    protected $casts = [
        'points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function conductRecords(): HasMany
    {
        return $this->hasMany(StudentConductRecord::class);
    }
}
