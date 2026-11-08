<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('classroom_id')->constrained('subjects')->nullOnDelete();
            $table->foreignId('class_schedule_id')->nullable()->after('subject_id')->constrained('class_schedules')->nullOnDelete();
            $table->dropUnique('unique_student_date_attendance');
            $table->index(['student_id', 'date', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['class_schedule_id']);
            $table->dropForeign(['subject_id']);
            $table->dropColumn(['subject_id', 'class_schedule_id']);
            $table->unique(['student_id', 'date'], 'unique_student_date_attendance');
        });
    }
};
