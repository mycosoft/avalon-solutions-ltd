<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Polymorphic-style support: payment can be for a patient OR a caregiver
            $table->enum('payee_for', ['patient', 'caregiver'])->default('patient')->after('patient_id');
            $table->foreignId('caregiver_id')->nullable()->after('payee_for')->constrained('caregivers')->nullOnDelete();
            $table->decimal('monthly_rate', 12, 2)->nullable()->after('daily_rate');

            // Make patient_id nullable so caregiver payments can omit it
            $table->foreignId('patient_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['caregiver_id']);
            $table->dropColumn(['payee_for', 'caregiver_id', 'monthly_rate']);
            $table->foreignId('patient_id')->nullable(false)->change();
        });
    }
};
