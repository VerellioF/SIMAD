<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppdb_registrations', function (Blueprint $table) {
            $table->unsignedInteger('registration_fee')->default(50000)->after('file_photo');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after('registration_fee');
            $table->enum('payment_method', ['transfer', 'cash'])->default('transfer')->after('payment_status');
            $table->string('file_payment_proof', 255)->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('file_payment_proof');

            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('ppdb_registrations', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropColumn([
                'registration_fee',
                'payment_status',
                'payment_method',
                'file_payment_proof',
                'paid_at',
            ]);
        });
    }
};
