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
        Schema::create('attendance_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');
            $table->foreignId('record_id')->nullable()->constrained('records')->onDelete('cascade');
            $table->string('type_of_work')->nullable();
            $table->text('activity')->nullable();
            $table->string('notes')->nullable();
            $table->integer('total_minute')->default(0);
            $table->string('task_name')->nullable();
            $table->string('description')->nullable();
            $table->string('output')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_times');
    }
};
