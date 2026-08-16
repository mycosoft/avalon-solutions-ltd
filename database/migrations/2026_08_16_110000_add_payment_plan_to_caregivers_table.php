<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('caregivers', function (Blueprint $table) {
            $table->enum('payment_plan', ['daily', 'monthly'])
                ->default('daily')
                ->after('monthly_rate');
        });
    }

    public function down(): void
    {
        Schema::table('caregivers', function (Blueprint $table) {
            $table->dropColumn('payment_plan');
        });
    }
};