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
            $table->string('softwares')->nullable(); // Or you can use a foreign key if you have a software table
            $table->string('industry')->nullable(); // Same here for the industry
            $table->string('highest_education')->nullable();
            $table->string('university')->nullable();
            $table->string('pdf_resume')->nullable(); // Store the path of the PDF file
            $table->unsignedBigInteger('job_id')->nullable();
            $table->unsignedBigInteger('software_id')->nullable(); // Assuming this is related to a software table
            $table->unsignedBigInteger('industry_id')->nullable(); // Assuming this is related to an industry table
            $table->timestamps();

            $table->foreign('job_id')->references('id')->on('job_offers'); // If you have a jobs table
            $table->foreign('software_id')->references('id')->on('softwares'); // If you have a softwares table
            $table->foreign('industry_id')->references('id')->on('industries');
        
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
