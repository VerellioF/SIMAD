<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GalleryCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'category' => GalleryCategory::class,
        ];
    }
}
