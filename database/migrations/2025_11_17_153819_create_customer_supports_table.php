<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_supports', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('end_user_id')->constrained('end_users')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('solution_id')->nullable()->constrained('solutions')->cascadeOnDelete();
            $table->foreignId('software_id')->nullable()->constrained('softwares')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('issue_type')->nullable();
            $table->string('priority_level')->nullable();
            $table->string('subject')->nullable();
            $table->string('attachment')->nullable();
            $table->date('date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('call_type')->nullable();
            $table->string('priority')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('record_call')->default(0);
            $table->tinyInteger('allow_guests')->default(0);
            $table->tinyInteger('send_reminders')->default(0);
            $table->dateTime('start_datetime')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('chat_type')->nullable();
            $table->tinyInteger('allow_file_sharing')->default(0);
            $table->tinyInteger('allow_anonymous')->default(0);
            $table->string('ticket_number')->nullable();
            $table->string('company_name')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_supports');
    }
};