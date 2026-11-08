<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extracurriculars', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->enum('category', ['keagamaan', 'seni_budaya', 'olahraga', 'kepanduan', 'sains'])->default('keagamaan');
            $table->string('instructor_name', 150);
            $table->string('schedule_day', 30);
            $table->string('schedule_time', 50);
            $table->string('location', 100)->default('Madrasah');
            $table->text('description')->nullable();
            $table->string('image_path', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('extracurricular_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('extracurricular_id')->constrained('extracurriculars')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->enum('status', ['active', 'completed', 'dropped'])->default('active');
            $table->timestamps();

            $table->unique(['student_id', 'extracurricular_id', 'academic_year_id'], 'student_ekskul_year_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extracurricular_enrollments');
        Schema::dropIfExists('extracurriculars');
    }
};
