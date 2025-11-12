<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->nullable()->constrained('records')->cascadeOnDelete('set null');
            $table->foreignId('deal_id')->nullable()->constrained('records')->cascadeOnDelete('set null');
            $table->string('forwarding_letter');
            $table->string('deal_name')->nullable();
            $table->string('software_area')->nullable();
            $table->string('software_name')->nullable();
            $table->string('industry')->nullable();
            $table->string('service_type')->nullable();
            $table->double('proposal_amount')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->text('special_terms_and_conditions')->nullable();
            $table->text('attachment')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};


