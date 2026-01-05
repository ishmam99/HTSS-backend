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
            $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('job_type');
            $table->string('location_type');
            $table->string('base_country')->nullable();
            $table->string('required_experience');
            $table->json('requirements')->nullable();
            $table->json('required_qualifications');
            $table->json('key_skills')->nullable();
            $table->json('primary_software')->nullable();
            $table->dateTime('deadline');
            $table->integer('number_of_vacancies')->default(0);
            $table->double('salary_min')->default(0);
            $table->double('salary_max')->default(0);
            $table->dateTime('published_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->text('benefits')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=closed,1=draft,2=published');
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
