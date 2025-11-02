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
        Schema::create('solution_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_schedule_id')->constrained('training_schedules')->cascadeOnDelete();
            $table->string('title');
            $table->text('objective')->nullable();
            $table->text('content')->nullable();
            $table->string('material_link')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->tinyInteger('level')->default(1);    // 1 = basic, 2 = intermediate, 3 = advanced
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solution_trainings');
    }
};
