<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->string('name', 150); // e.g. "Al-Qur'an Hadits", "Tahfidz Juz 'Amma", "Matematika Nalaria Realistik"
            $table->string('code', 20)->nullable();
            $table->enum('phase', ['fase_a', 'fase_b', 'fase_c'])->default('fase_a'); // fase_a: Kelas 1-2, fase_b: Kelas 3-4, fase_c: Kelas 5-6
            $table->enum('curriculum_type', ['kemenag_pai', 'nasional_merdeka', 'muatan_lokal_pesantren'])->default('kemenag_pai');
            $table->unsignedTinyInteger('hours_per_week')->default(2); // Jam tatap muka (JP) per minggu
            $table->text('competency_goals')->nullable(); // Capaian Pembelajaran (CP) / Target Kompetensi
            $table->text('syllabus_summary')->nullable(); // Ringkasan Materi Pokok
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();

            $table->index(['phase', 'curriculum_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_structures');
    }
};
