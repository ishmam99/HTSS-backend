<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
                  $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index(); // salesperson
            $table->string('action');              // viewed, created, updated, deleted, login...
            $table->string('module')->nullable()->index(); // Account, Lead, Deal, Contact...
            $table->unsignedBigInteger('record_id')->nullable()->index();
            $table->text('details')->nullable();   // JSON text (old/new/etc)
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('meta')->nullable();    // searchable small meta (e.g. "phone_changed")
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
