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
        Schema::create('training_requests', function (Blueprint $table) {
            $table->id();
            
            // Personal Information
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('organization')->nullable();
            $table->string('job_title')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            // Course Information
            $table->unsignedBigInteger('course_id')->nullable();
            $table->foreignId('training_course_schedule_id')->nullable()->constrained('training_course_schedules')->cascadeOnDelete();
            $table->string('course_name')->nullable();
            $table->string('course_code')->nullable();
            $table->string('training_type')->nullable(); 
            $table->string('software')->nullable();
            $table->string('solution_area')->nullable(); // Structure, Fluids, Acoustics
            $table->string('experience_level')->nullable(); // beginner, intermediate, advanced
            $table->decimal('course_price', 10, 2)->nullable();
            
            // Training Preferences
            $table->string('preferred_format')->nullable(); // online, onsite, hybrid
            $table->date('preferred_start_date')->nullable();
            $table->string('preferred_timezone')->nullable();
            $table->integer('number_of_participants')->nullable()->default(1);
            
            // Additional Information
            $table->text('comments')->nullable();
            $table->text('specific_goals')->nullable();
            $table->text('previous_experience')->nullable();
            
            // Status Tracking
            $table->enum('status', [
                'pending', 
                'under_review', 
                'approved', 
                'scheduled', 
                'completed', 
                'cancelled', 
                'rejected'
            ])->default('pending');
            
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            
            // Scheduling Information
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('location')->nullable(); // For onsite training
            
            // Payment Information (if applicable)
            $table->enum('payment_status', ['pending', 'paid', 'waived', 'not_required'])->default('not_required');
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Completion & Certificate
            $table->timestamp('completed_at')->nullable();
            $table->boolean('certificate_issued')->default(false);
            $table->string('certificate_url')->nullable();
            $table->text('feedback')->nullable();
            $table->integer('rating')->nullable()->min(1)->max(5);
            
            // Tracking
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('source_page')->nullable(); // Where the request came from
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index(['email', 'status']);
            $table->index(['course_code', 'training_type']);
            $table->index('status');
            $table->index('created_at');
            $table->index('preferred_start_date');
            
            // Foreign keys
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_requests');
    }
};