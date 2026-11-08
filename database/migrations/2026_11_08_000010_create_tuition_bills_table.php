<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tuition_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('bill_title', 150);
            $table->unsignedTinyInteger('month')->nullable(); // 1 - 12
            $table->year('year');
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['unpaid', 'pending_verification', 'paid'])->default('unpaid');
            $table->date('due_date');
            $table->timestamps();

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tuition_bills');
    }
};
