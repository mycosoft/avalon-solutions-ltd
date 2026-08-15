<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caregiver_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('ward')->nullable();
            $table->integer('days_under_care')->default(0);
            $table->text('admin_observation')->nullable();
            $table->text('complaint_reported')->nullable();
            $table->text('complaint_assignment')->nullable();
            $table->text('follow_up')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
