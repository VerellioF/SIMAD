<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahfidz_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date');
            $table->string('surah_name', 100);
            $table->string('verse_range', 50); // e.g. 'Ayat 1-20'
            $table->enum('fluency_grade', ['lancar', 'kurang_lancar', 'mengulang']);
            $table->enum('tajweed_grade', ['mumtaz', 'jayyid', 'maqbul']);
            $table->string('audio_recording_path', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahfidz_records');
    }
};
