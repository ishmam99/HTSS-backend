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
        Schema::create('trainer_preferd_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('trainer_request_form_id')->nullable()->constrained('trainer_request_forms')->cascadeOnDelete();
            $table->json('days');
            $table->tinyInteger('status')->default(0);
            $table->time('start_time');
             $table->time('end_time');
          
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_preferd_schedules');
    }
};
