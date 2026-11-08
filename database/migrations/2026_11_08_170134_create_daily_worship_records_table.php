<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_worship_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('date');
            $table->boolean('subuh')->default(false);
            $table->boolean('dzuhur')->default(false);
            $table->boolean('ashar')->default(false);
            $table->boolean('maghrib')->default(false);
            $table->boolean('isya')->default(false);
            $table->boolean('dhuha')->default(false);
            $table->boolean('tadarus_quran')->default(false);
            $table->boolean('birrul_walidain')->default(false);
            $table->string('parent_notes', 255)->nullable();
            $table->string('teacher_notes', 255)->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_worship_records');
    }
};
