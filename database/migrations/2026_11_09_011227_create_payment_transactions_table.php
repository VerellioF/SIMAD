<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 64)->unique();
            $table->string('payable_type');
            $table->unsignedBigInteger('payable_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('payer_name', 150);
            $table->string('payer_email', 150)->nullable();
            $table->string('payer_phone', 30);

            $table->decimal('amount', 12, 2);
            $table->decimal('fee_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);

            $table->string('payment_method', 50)->default('qris'); // qris, bsi_va, bca_va, etc.
            $table->string('payment_provider', 50)->default('simulator'); // simulator, tripay, midtrans
            $table->string('provider_reference', 100)->nullable();

            $table->text('qr_string')->nullable();
            $table->text('qr_url')->nullable();
            $table->text('checkout_url')->nullable();

            $table->enum('status', ['pending', 'paid', 'expired', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->json('raw_payload')->nullable();

            $table->timestamps();

            $table->index(['payable_type', 'payable_id']);
            $table->index('status');
        });

        // Ensure tuition_payments.payment_method allows strings like 'qris_gateway'
        if (Schema::hasTable('tuition_payments')) {
            Schema::table('tuition_payments', function (Blueprint $table) {
                $table->string('payment_method', 50)->default('transfer_bank')->change();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
