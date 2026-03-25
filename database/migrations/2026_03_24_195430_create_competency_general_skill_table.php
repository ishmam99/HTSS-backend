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
        Schema::create('competency_general_skill', function (Blueprint $table) {
            $table->id();
           $table->foreignId('general_skill_id')
                  ->constrained('general_skills')
                  ->onDelete('cascade');
            $table->foreignId('competency_id')
                  ->constrained('competencies')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competencies');
    }
};
