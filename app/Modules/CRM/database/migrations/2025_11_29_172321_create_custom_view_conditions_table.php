<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_view_conditions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('custom_view_group_id')->constrained('custom_view_groups')->cascadeOnDelete();

            $table->string('field');
            $table->string('operator');
            $table->string('value')->nullable(); // json if multiple

            $table->integer('order')->default(0);
            
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('custom_view_conditions');
    }
};
