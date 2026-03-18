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
       Schema::create('end_user_road_maps', function (Blueprint $table) {
    $table->id();

    $table->foreignId('end_user_id')
          ->constrained('end_users')
          ->cascadeOnDelete();

    $table->string('type');

    $table->foreignId('training_course_id')
          ->nullable()
          ->constrained('training_courses')
          ->cascadeOnDelete();

    $table->foreignId('software_id')
          ->nullable()
          ->constrained('softwares')
          ->cascadeOnDelete();

    $table->foreignId('solution_id')
          ->nullable()
          ->constrained('solutions')
          ->cascadeOnDelete();

    $table->string('title');            
    $table->text('description')->nullable(); 

    $table->date('start_date')->nullable();
    $table->date('end_date')->nullable();

    $table->integer('progress')->default(0); 

    $table->enum('status', ['planned', 'in_progress', 'completed', 'paused'])
          ->default('planned');

    $table->enum('priority', ['low', 'medium', 'high'])
          ->default('medium');

    $table->boolean('is_reminder_enabled')->default(false);

    $table->integer('estimated_hours')->nullable();
    $table->integer('actual_hours')->nullable();

    // Meta
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('end_user_road_maps');
    }
};
