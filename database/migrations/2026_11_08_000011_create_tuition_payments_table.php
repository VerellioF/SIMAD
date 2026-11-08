<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tuition_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tuition_bill_id')->constrained('tuition_bills')->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('receipt_number', 50)->unique();
            $table->decimal('amount_paid', 12, 2);
            $table->enum('payment_method', ['transfer_bank', 'tunai_madrasah'])->default('transfer_bank');
            $table->string('bank_sender_name', 100)->nullable();
            $table->string('proof_attachment', 255)->nullable();
            $table->date('payment_date');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tuition_payments');
    }
};
