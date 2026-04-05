<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applied_jobs', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('contact')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('address')->nullable();

            // Personal Info
            $table->string('marital_status')->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('spouse_number')->nullable();

            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();

            // Optional Family (generic)
            $table->string('parent_name')->nullable();
            $table->string('parent_relation')->nullable();
            $table->string('parent_phone_number')->nullable();

            $table->string('siblings_name')->nullable();
            $table->string('siblings_relation')->nullable();
            $table->string('siblings_phone_number')->nullable();

            // Education & Work
            $table->string('highest_education')->nullable();
            $table->string('university')->nullable();
            $table->integer('experience_years')->nullable();

            $table->string('company_name')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();

            // Skills / System Info
            $table->string('system')->nullable();
            $table->string('softwares')->nullable();
            $table->string('industry')->nullable();

            // Resume & Links
            $table->string('resume')->nullable();
            $table->string('link')->nullable(); // portfolio / linkedin etc

            // Salary
            $table->decimal('expected_salary', 10, 2)->nullable();
            $table->decimal('negotiated_salary', 10, 2)->nullable();

            // Evaluation (HR Scoring)
            $table->tinyInteger('technical_skills')->nullable(); // 1-10
            $table->tinyInteger('communication')->nullable();
            $table->tinyInteger('cultural_fit')->nullable();
            $table->tinyInteger('problem_solving')->nullable();

            $table->text('overall_comment')->nullable();
            $table->enum('recommendation', ['hire', 'no_hire', 'hold'])->nullable();

            // References (static 2 for now)
            $table->string('reference_one_name')->nullable();
            $table->string('reference_one_number')->nullable();
            $table->string('reference_one_designation')->nullable();
            $table->string('reference_one_email')->nullable();

            $table->string('reference_two_name')->nullable();
            $table->string('reference_two_number')->nullable();
            $table->string('reference_two_designation')->nullable();
            $table->string('reference_two_email')->nullable();

            // HR Process / Verification
            $table->boolean('terms_accepted')->default(false);
            $table->boolean('signature_uploaded')->default(false);
            $table->string('signature_path')->nullable();

            $table->boolean('reference_checked')->default(false);
            $table->boolean('background_verified')->default(false);
            $table->boolean('documents_verified')->default(false);

            $table->boolean('educational_background_check')->default(false);
            $table->boolean('professional_background_check')->default(false);
            $table->boolean('experience_background_check')->default(false);
            $table->string('educational_background_check_document')->nullable();
            $table->string('experience_background_check_document')->nullable();
            $table->string('police_background_check_document')->nullable();
           

            // Status Tracking
            $table->tinyInteger('status')->default(0); 
           

            // Relations
            $table->foreignId('job_id')->nullable()->constrained('job_offers')->cascadeOnDelete();
            $table->foreignId('software_id')->nullable()->constrained('softwares')->cascadeOnDelete();
            $table->foreignId('industry_id')->nullable()->constrained('industries')->cascadeOnDelete();

            $table->text('responsibilities')->nullable();
            $table->text('benefits')->nullable();
            $table->text('employment_terms')->nullable();
            $table->text('terms_clauses')->nullable();
            $table->string('access_token')->nullable()->unique();
            $table->timestamp('access_token_expires_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('offering_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applied_jobs');
    }
};