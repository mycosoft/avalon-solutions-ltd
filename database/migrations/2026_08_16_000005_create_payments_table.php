<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('payee_name')->comment('Client name or relative');
            $table->decimal('amount_paid', 12, 2);
            $table->decimal('daily_rate', 12, 2);
            $table->integer('days_paid');
            $table->date('payment_date');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('payment_method')->default('cash');
            $table->enum('payment_type', ['full', 'partial'])->default('full');
            $table->decimal('balance', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('recorded_by')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
