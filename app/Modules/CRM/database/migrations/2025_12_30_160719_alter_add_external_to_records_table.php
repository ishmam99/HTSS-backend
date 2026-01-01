<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /** RECORDS */

Schema::table('records', function (Blueprint $table) {
    if (!Schema::hasColumn('records', 'external_id')) {
        $table->string('external_id')->nullable()->index();
    }
});

$indexExists = DB::select("
    SHOW INDEX FROM records WHERE Key_name = 'records_module_external_unique'
");

if (empty($indexExists)) {
    Schema::table('records', function (Blueprint $table) {
        $table->unique(
            ['module_id', 'external_id'],
            'records_module_external_unique'
        );
    });
}

        /** RECORD VALUES */
        Schema::table('record_values', function (Blueprint $table) {
            $table->unique(
                ['record_id', 'field_id'],
                'record_values_record_field_unique'
            );
        });

        /** RECORD RELATIONS */
        Schema::table('record_relations', function (Blueprint $table) {
            $table->unique(
                ['parent_record_id', 'child_record_id', 'relation_type'],
                'record_relations_unique'
            );
        });

        /** MODULE FIELDS */
        Schema::table('module_fields', function (Blueprint $table) {
            if (!Schema::hasColumn('module_fields', 'is_duplicate_key')) {
                $table->boolean('is_duplicate_key')->default(false);
            }
        });

        /** IMPORT ERRORS */
        Schema::create('import_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->json('row_data');
            $table->string('error');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        /** IMPORT ERRORS */
        Schema::dropIfExists('import_errors');

        /** MODULE FIELDS */
        Schema::table('module_fields', function (Blueprint $table) {
            if (Schema::hasColumn('module_fields', 'is_duplicate_key')) {
                $table->dropColumn('is_duplicate_key');
            }
        });

        /** RECORD RELATIONS */
        Schema::table('record_relations', function (Blueprint $table) {
            $table->dropUnique('record_relations_unique');
        });

        /** RECORD VALUES */
        Schema::table('record_values', function (Blueprint $table) {
            $table->dropUnique('record_values_record_field_unique');
        });

        /** RECORDS */
        Schema::table('records', function (Blueprint $table) {
            $table->dropUnique('records_module_external_unique');

            if (Schema::hasColumn('records', 'external_id')) {
                $table->dropColumn('external_id');
            }
        });
    }
};
