<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->string('nis', 30)->nullable()->unique();
            $table->string('nisn', 30)->nullable()->unique();
            $table->string('full_name', 255);
            $table->string('nickname', 100)->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place', 100);
            $table->date('birth_date');
            $table->text('address');
            $table->enum('status', ['active', 'graduated', 'transferred', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('classroom_id');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
