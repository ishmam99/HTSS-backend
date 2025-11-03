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
        Schema::create('industry_solutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industry_id')->constrained('industries')->cascadeOnDelete();
            $table->foreignId('solution_id')->constrained('solutions')->cascadeOnDelete();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industry_solutions');
    }
};
