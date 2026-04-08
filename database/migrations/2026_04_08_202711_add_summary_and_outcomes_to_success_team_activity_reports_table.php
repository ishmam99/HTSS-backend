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
        Schema::table('success_team_activity_reports', function (Blueprint $table) {
            $table->json('summary_activities')->nullable()->after('period');
            $table->json('key_outcomes')->nullable()->after('summary_activities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('success_team_activity_reports', function (Blueprint $table) {
            //
        });
    }
};
