<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('end_user_id')->constrained('end_users')->cascadeOnDelete();
            $table->foreignId('training_offer_id')->constrained('training_offers')->cascadeOnDelete();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_enrollments');
    }
};
