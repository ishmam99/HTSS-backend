<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('record_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_record_id')->constrained('records')->cascadeOnDelete();
            $table->foreignId('child_record_id')->constrained('records')->cascadeOnDelete();
            $table->string('relation_type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_relations');
    }
};
