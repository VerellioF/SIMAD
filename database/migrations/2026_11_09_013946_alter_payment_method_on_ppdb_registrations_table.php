<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ppdb_registrations')) {
            Schema::table('ppdb_registrations', function (Blueprint $table) {
                $table->string('payment_method', 50)->default('qris')->change();
            });
        }
    }

    public function down(): void
    {
        // No-op rollback
    }
};
