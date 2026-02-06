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
        Schema::create('success_team_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('success_team_id')->constrained('success_teams')->cascadeOnDelete();
            $table->foreignId('solution_id')->constrained('solutions')->cascadeOnDelete();
            $table->foreignId('software_id')->nullable()->constrained('softwares')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->cascadeOnDelete();
            $table->text('description');
            $table->string('status')->default('pending');
            $table->string('type')->nullable();
            $table->date('date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('success_team_tasks');
    }
};
