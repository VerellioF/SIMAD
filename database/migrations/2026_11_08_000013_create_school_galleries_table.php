<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->enum('category', ['seni_pentas', 'hadrah_rebana', 'pramuka', 'fasilitas', 'prestasi']);
            $table->string('image_path', 255);
            $table->timestamps();

            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_galleries');
    }
};
