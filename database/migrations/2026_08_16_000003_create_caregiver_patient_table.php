<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caregiver_patient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caregiver_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->date('assignment_date')->default(now());
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caregiver_patient');
    }
};
