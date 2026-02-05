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
        Schema::create('success_team_task_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('success_team_task_id')->constrained('success_team_tasks')->cascadeOnDelete();
            $table->text('output')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->date('date');
            $table->date('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('success_team_task_outputs');
    }
};
