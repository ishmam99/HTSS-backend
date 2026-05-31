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
        Schema::create('trainer_skills', function (Blueprint $table) {
               $table->id();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('trainer_request_form_id')->nullable()->constrained('trainer_request_forms')->cascadeOnDelete();
            $table->tinyInteger('skill_type')->default(0);
            $table->foreignId('software_id')->nullable()->constrained('softwares')->cascadeOnDelete();
            $table->string('level')->nullable();
            $table->foreignId('solution_id')->nullable()->constrained('solutions')->cascadeOnDelete();
            $table->string('analysis')->nullable();
            $table->tinyInteger('status')->default(0);
         
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_skills');
    }
};
