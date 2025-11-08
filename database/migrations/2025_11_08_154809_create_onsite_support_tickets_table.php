<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onsite_support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->nullable();
            $table->string('company_name')->nullable();
            $table->string('location')->nullable();
            $table->string('issue_type')->nullable();
            $table->string('priority_level')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onsite_support_tickets');
    }
};
