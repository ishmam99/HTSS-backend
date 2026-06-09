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
        Schema::create('software_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('record_id')->nullable();
            $table->string('email');
            $table->string('name');
            $table->string('company_name');
            $table->foreignId('software_id')->constrained('softwares')->onDelete('cascade');
            $table->foreignId('solution_id')->constrained('solutions')->onDelete('cascade');
           $table->string('phone')->nullable();
           $table->string('billing_street')->nullable();
           $table->string('billing_city')->nullable();
           $table->string('billing_country')->nullable();
           $table->string('billing_state')->nullable();
            $table->json('account_data')->nullable();
            $table->tinyInteger('status')->default(0); // 0 = pending, 1 = approved, 2 = rejected
            $table->tinyInteger('is_converted')->default(0); // 0 = pending, 1 = approved, 2 = rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_requests');
    }
};
