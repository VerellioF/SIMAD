<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AnnouncementTarget;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'target_audience',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'target_audience' => AnnouncementTarget::class,
            'valid_until' => 'date',
        ];
    }
}
