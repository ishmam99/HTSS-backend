<?php

namespace Modules\CRM\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class LeedsFieldSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            ['Company', 'T'],
            ['Industry', 'DD'],
            ['Leads Source', 'DD'],
            ['Street', 'T'],
            ['State', 'T'],
            ['Country', 'T'],
            ['City', 'T'],
            ['Zip Code', 'T'],
            ['County', 'T'],
            ['Owner Contact Name', 'T'],
            ['Owner Phone', 'T'],
            ['Owner City Newton', 'T'],
            ['Owner Contact Title', 'T'],
            ['Owner Website', 'T'],
            ['Owner Company Name', 'T'],
            ['Owner E-Mail', 'T'],
            ['Owner Address', 'T'],
            ['Owner -State', 'T'],
            ['Owner -Zip Code', 'T'],
            ['Owner -Fax', 'T'],
            ['Customer Type', 'T'],
            ['Project Name', 'T'],
            ['Project Summary', 'T'],
            ['Project Type', 'T'],
            ['Original Data Input Date', 'Date'],
            ['PhaseOfBusiness/ActionStageType', 'T'],
            ['News and Notes', 'T'],
            ['BuildingCategory/ConstructionType', 'T'],
            ['HTB GRP Industry Name', 'DD'],
            ['Hoovers -Dodge Industry Name', 'DD'],
            ['M05 Dodge Industry Sub-sub-class Name', 'DD'],
            ['HTB GRP Sub Industry Name', 'DD'],
            ['M04 Hoovers -Dodge Sub-Industry Name', 'DD'],
            ['Project Number', 'T', true], // Unique
            ['Construction Start Date', 'Date'],
            ['Construction End Date', 'Date'],
            ['Architect Contact Name', 'T'],
            ['Architect Contact Name 2', 'T'],
            ['Architect Company Name', 'T'],
            ['Architect Website', 'T'],
            ['Architect Address', 'T'],
            ['Architect Phone', 'T'],
            ['Architect Phone 2', 'T'],
            ['Architect Contact Title', 'T'],
            ['Architect Contact Title 2', 'T'],
            ['Architect Location Phone 1', 'T'],
            ['Architect Location Phone 2', 'T'],
            ['GC Company Name', 'T'],
            ['GC Contact Name 1', 'T'],
            ['GC Contact Name 2', 'T'],
            ['GC Phone 1', 'T'],
            ['GC Phone 2', 'T'],
            ['GC City', 'T'],
            ['GC State', 'T'],
            ['GC E-Mail', 'T'],
            ['GC Zip Code', 'T'],
            ['GC Contact Title 1', 'T'],
            ['GC Contact Title 2', 'T'],
            ['GC Company Website', 'T'],
            ['GC Location Phone 1', 'T'],
            ['GC Location Phone 2', 'T'],
            ['Developer Company Name', 'T'],
            ['Developer Contact Name 1', 'T'],
            ['Developer Contact Name 2', 'T'],
            ['Developer Phone 1', 'T'],
            ['Developer Phone 2', 'T'],
            ['Developer City', 'T'],
            ['Developer State', 'T'],
            ['Developer E-Mail', 'T'],
            ['Developer Zip Code', 'T'],
            ['Developer Contact Title 1', 'T'],
            ['Developer Contact Title 2', 'T'],
            ['Developer Company Website', 'T'],
            ['Developer Location Phone 1', 'T'],
            ['Developer Location Phone 2', 'T'],
        ];

        foreach ($fields as $f) {
            DB::table('module_fields')->insert([
                'module_id' => 1,
                'label' => $f[0],
                'name' => Str::snake(Str::replace('/', '_', Str::replace('-', '_', $f[0]))),
                'type' => match (strtoupper($f[1])) {
                    'T' => 'text',
                    'DD' => 'select',
                    'DATE' => 'date',
                    default => 'text'
                },
                'required' => false,
                'unique' => isset($f[2]) && $f[2] === true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
