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
        Schema::create('meeting_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_title');
            $table->text('description')->nullable();
            $table->date('date');
            $table->string('duration');
            $table->time('time');
            $table->string('timezone');
            $table->string('meeting_type');
            $table->string('meeting_link');
            $table->string('priority');
            $table->string('type_of_activity')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('success_team_id')->constrained('success_teams')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_schedules');
    }
};
