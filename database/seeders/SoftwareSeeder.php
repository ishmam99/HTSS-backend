<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Software;
use Illuminate\Support\Facades\DB;

class SoftwareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Software::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $softwares = [
    'Patran',
    'Nastran',
    'Marc',
    'MSC Fatigue',
    'Adams',
    'Sim Manager',
    'Dytran',
    'MSC Apex',
    'Romax',
    'Easy 5',
    'Elements',
    'Actran',
    'MSC Cradle CFD',
    'MSCCoSim',
    'VTDScale',
    'VTD',
    'Cloud',
    'ODYSSEE',
    'Simufact',
    'FTI FormingSuite',
    'MaterialCenter',
    'Digimat',
    'MaterialCenterDatabanks'
        ];
        foreach($softwares as $software)
        {
             Software::create(['name'=>$software]);
        }


    }
}
