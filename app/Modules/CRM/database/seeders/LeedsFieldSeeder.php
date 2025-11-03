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
            ['Parent Company', 'T'],
            ['Company Name', 'T'],
            ['Industry', 'DD' ,['Aerospace','Automovitve','Electronics','Defense']],
            ['Leads Source', 'DD',['Our Own Source']],
            ['Street', 'T'],
            ['State', 'T'],
            ['Country', 'T'],
            ['City', 'T'],
            ['Zip Code', 'T'],
            ['County', 'T'],
            ['Customer Type', 'T'],
            ['Original Data Input Date', 'Date'],

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
                    default => 'text',

                },
                'required' => false,
                'unique' => isset($f[3]) && $f[3] === true,
                'created_at' => now(),
                'updated_at' => now(),
                'options' => isset($f[2]) ? json_encode($f[2]):null,
            ]);
        }
    }
}
