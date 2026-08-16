<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['on_ward', 'transferred', 'discharged']);
            $table->date('effective_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_status_logs');
    }
};
