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
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->string('title');
            $table->text('overview');
            $table->string('job_type');
            $table->string('location_type');
            $table->string('base_country');
            $table->string('required_experience');
            $table->json('key_responsibilities');
            $table->json('required_qualifications');
            $table->json('key_skills');
            $table->json('primary_software');
            $table->dateTime('deadline');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_offers');
    }
};
