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
        Schema::table('software', function (Blueprint $table) {
            $table->string('name');
            $table->string('vendor')->nullable();
            $table->string('version')->nullable();
            $table->date('release_date')->nullable();
            $table->integer('status')->default(0);
            $table->foreignId('software_skill_id')->nullable()->constrained('software_skills')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_name', function (Blueprint $table) {
            //
        });
    }
};
