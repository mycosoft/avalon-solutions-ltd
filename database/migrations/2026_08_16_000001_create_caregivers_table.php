<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caregivers', function (Blueprint $table) {
            $table->id();
            $table->string('photo')->nullable();
            $table->string('name');
            $table->string('address');
            $table->string('phone', 20);
            $table->string('nin', 20)->comment('National Identification Number');
            $table->date('date_of_birth');
            $table->string('gender');
            $table->string('level_of_education')->nullable();
            $table->date('date_of_entry');
            $table->string('next_of_kin_name');
            $table->string('next_of_kin_relationship');
            $table->string('next_of_kin_phone', 20);
            $table->string('next_of_kin_address');
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caregivers');
    }
};
