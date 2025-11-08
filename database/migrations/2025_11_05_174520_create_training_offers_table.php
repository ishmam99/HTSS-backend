<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_event_id')->constrained('training_events')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('location')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('available_seats')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_offers');
    }
};
