<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('phone', 20);
            $table->string('gender');
            $table->string('relative_name')->nullable();
            $table->string('ward')->nullable();
            $table->decimal('amount_to_pay', 12, 2)->default(0);
            $table->date('date_of_admission');
            $table->enum('patient_status', ['on_ward', 'transferred', 'discharged'])->default('on_ward');
            $table->date('date_of_discharge')->nullable();
            $table->date('date_of_transfer')->nullable();
            $table->string('next_of_kin_name');
            $table->string('next_of_kin_relationship');
            $table->string('next_of_kin_phone', 20);
            $table->string('next_of_kin_address');
            $table->text('transfer_notes')->nullable();
            $table->text('discharge_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
