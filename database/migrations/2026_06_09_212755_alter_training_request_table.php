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
         Schema::table('training_requests', function (Blueprint $table) {
            $table->string('receipt_url')->nullable()->after('course_id');
          
        });
         Schema::table('training_enrollments', function (Blueprint $table) {
            $table->string('receipt_url')->nullable()->after('transaction_id');
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
