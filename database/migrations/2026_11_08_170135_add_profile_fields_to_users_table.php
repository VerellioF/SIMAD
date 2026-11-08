<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('position', 100)->nullable()->after('role');
            $table->string('education', 150)->nullable()->after('position');
            $table->text('bio')->nullable()->after('education');
            $table->unsignedSmallInteger('display_order')->default(0)->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['position', 'education', 'bio', 'display_order']);
        });
    }
};
