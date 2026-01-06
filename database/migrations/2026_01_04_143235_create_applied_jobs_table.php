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
        Schema::create('applied_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('contact')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('system')->nullable();
            $table->string('softwares')->nullable();
            $table->string('industry')->nullable();
            $table->string('highest_education')->nullable();
            $table->string('university')->nullable();
            $table->string('resume')->nullable(); 
            $table->foreignId('job_id')->nullable()->constrained('job_offers')->cascadeOnDelete();
            $table->foreignId('software_id')->nullable()->constrained('softwares')->cascadeOnDelete();
            $table->foreignId('industry_id')->nullable()->constrained('industries')->cascadeOnDelete();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applied_jobs');
    }
};
