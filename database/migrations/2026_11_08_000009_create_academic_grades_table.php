<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('grade_type', ['harian', 'tugas', 'pts', 'pas']);
            $table->decimal('score', 5, 2); // 0.00 to 100.00
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index(['student_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_grades');
    }
};
