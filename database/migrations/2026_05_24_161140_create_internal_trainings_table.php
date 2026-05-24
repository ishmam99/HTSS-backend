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
        Schema::create('internal_trainings', function (Blueprint $table) {
            $table->id();
             $table->string('name');
            $table->string('code')->unique();
            $table->tinyInteger('status')->default(0);
            $table->string('short_description')->nullable();
            $table->longText('long_description')->nullable();

            $table->string('duration')->nullable();

            $table->foreignId('software_id')->nullable()->constrained('softwares')->cascadeOnDelete();
            $table->string('level')->nullable();

            $table->decimal('price', 10, 2)->default(0);

            // Example: onsite, online, hybrid
            $table->enum('type', ['onsite', 'online', 'hybrid'])->default('onsite');

            $table->foreignId('solution_id')->nullable()->constrained('solutions')->cascadeOnDelete();

            // Example: Multibody Dynamic Analysis
            $table->string('analysis')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_trainings');
    }
};
