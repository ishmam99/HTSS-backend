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
        Schema::create('employees', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->foreignId('position_id')
        ->nullable()
        ->constrained('positions')
        ->cascadeOnDelete();

    $table->foreignId('department_id')
        ->constrained('departments')
        ->cascadeOnDelete();

    $table->string('first_name');
    $table->string('last_name');
    $table->string('email');
    $table->string('employee_uid');

    $table->date('joined_at');
    $table->date('left_at')->nullable();

    $table->tinyInteger('status')->default(0);

    $table->timestamps();
    $table->softDeletes();

    $table->string('type')->nullable();
    $table->string('hired_form')->nullable();
    $table->string('salary_type')->nullable();

    $table->double('salary')->default(0);

    $table->string('personal_email')->nullable();
    $table->string('contact_phone')->nullable();
    $table->string('optional_phone')->nullable();

    $table->string('father_name')->nullable();
    $table->string('mother_name')->nullable();

    $table->string('address')->nullable();

    $table->string('parent_name')->nullable();
    $table->string('parent_relation')->nullable();
    $table->string('parent_phone_number')->nullable();

    $table->string('siblings_name')->nullable();
    $table->string('siblings_relation')->nullable();
    $table->string('siblings_phone_number')->nullable();

    $table->tinyInteger('marital_status');

    $table->string('spouse_name')->nullable();
    $table->string('spouse_number')->nullable();

    $table->string('country')->nullable();

    $table->string('job_type')->nullable();
    $table->string('employment_status')->nullable();

    $table->string('offer_letter')->nullable();
    $table->string('nid')->nullable();
    $table->string('resume')->nullable();

    $table->string('reference_one_name')->nullable();
    $table->string('reference_one_number')->nullable();
    $table->string('reference_one_designation')->nullable();
    $table->string('reference_one_email')->nullable();

    $table->string('reference_two_name')->nullable();
    $table->string('reference_two_number')->nullable();
    $table->string('reference_two_designation')->nullable();
    $table->string('reference_two_email')->nullable();

    $table->string('last_education')->nullable();
    $table->string('last_educational_institution')->nullable();

    $table->boolean('previous_job_experience');

    $table->string('company_name')->nullable();
    $table->string('company_phone')->nullable();
    $table->string('company_email')->nullable();

    $table->integer('experience_years')->nullable();

});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
