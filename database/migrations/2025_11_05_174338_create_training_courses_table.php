<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_courses', function (Blueprint $table) {
            $table->id();
             $table->foreignId('industry_id')->constrained('industries')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->cascadeOnDelete();
            $table->string('training_type')->nullable();
            $table->string('solution_name');
            $table->string('software_name');
            $table->string('training_level');
            $table->string('title');
            $table->string('course_id')->nullable()->unique();
            $table->string('course_code')->nullable();
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_courses');
    }
};
