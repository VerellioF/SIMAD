<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PpdbStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpdbRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'child_name',
        'nik',
        'gender',
        'birth_place',
        'birth_date',
        'previous_school',
        'parent_name',
        'parent_whatsapp',
        'parent_job',
        'address',
        'file_birth_certificate',
        'file_family_card',
        'file_photo',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'status' => PpdbStatus::class,
        ];
    }
}
