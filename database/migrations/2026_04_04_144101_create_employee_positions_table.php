<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('reports_to')->nullable();
            $table->string('responsible_for')->nullable();
            $table->string('number_of_position')->nullable();
            $table->string('position_occupied')->nullable();
            $table->string('vacancy_available')->nullable();
            $table->string('salary_type')->nullable();
            $table->string('salary_range')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_positions');
    }
};
