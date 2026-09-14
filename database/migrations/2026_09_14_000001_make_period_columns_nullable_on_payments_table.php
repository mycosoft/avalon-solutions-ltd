<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Caregiver payments are attendance-based and may not have a
            // billing period, so these columns must allow null.
            $table->date('period_start')->nullable()->change();
            $table->date('period_end')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->date('period_start')->nullable(false)->change();
            $table->date('period_end')->nullable(false)->change();
        });
    }
};
