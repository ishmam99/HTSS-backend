<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('record_user_assignments', function (Blueprint $table) {
            $table->id();
              $table->foreignId('record_id')->constrained('records')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
                $table->string('role');
                $table->string('permission_level');
                $table->dateTime('assigned_at');
                 $table->unique(['record_id', 'user_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_user_assignments');
    }
};
