<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('character_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->enum('type', ['apresiasi', 'pelanggaran'])->default('apresiasi');
            $table->enum('category', ['akhlak_adab', 'kedisiplinan', 'prestasi', 'kebersihan_kerapian', 'ibadah'])->default('akhlak_adab');
            $table->integer('points')->default(5);
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_conduct_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('character_rule_id')->nullable()->constrained('character_rules')->nullOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['apresiasi', 'pelanggaran'])->default('apresiasi');
            $table->integer('points')->default(5);
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->date('incident_date');
            $table->string('follow_up_action', 255)->nullable(); // e.g. "Diberikan sertifikat santri teladan" atau "Diberikan nasehat & muroja'ah surat pendek"
            $table->timestamps();

            $table->index(['student_id', 'incident_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_conduct_records');
        Schema::dropIfExists('character_rules');
    }
};
