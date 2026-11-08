<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 50)->unique();
            $table->string('child_name', 255);
            $table->string('nik', 20);
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place', 100);
            $table->date('birth_date');
            $table->string('previous_school', 150);
            $table->string('parent_name', 255);
            $table->string('parent_whatsapp', 30);
            $table->string('parent_job', 100)->nullable();
            $table->text('address');
            $table->string('file_birth_certificate', 255);
            $table->string('file_family_card', 255);
            $table->string('file_photo', 255)->nullable();
            $table->enum('status', ['pending', 'verified', 'accepted', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('registration_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_registrations');
    }
};
