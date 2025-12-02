<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('custom_view_groups', function (Blueprint $table) {
                $table->id();

                $table->foreignId('custom_view_id')->constrained('custom_views')->cascadeOnDelete();
                $table->unsignedBigInteger('parent_id')->nullable(); // null = root group

                $table->enum('join_type', ['AND', 'OR'])->default('AND');

                $table->integer('order')->default(0);

                $table->timestamps();
            });

    }

    public function down(): void
    {
        Schema::dropIfExists('custom_view_groups');
    }
};
