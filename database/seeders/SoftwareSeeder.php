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
            ['name' => 'MSC Nastran'],
            ['name' => 'Patran'],
            ['name' => 'Marc'],
            ['name' => 'MSC Fatigue'],
            ['name' => 'Adams'],
            ['name' => 'Simufact Forming'],
            ['name' => 'Digimat'],
            ['name' => 'Apex'],
            ['name' => 'SimXpert'],
            ['name' => 'Actran'],
            ['name' => 'Easy5'],
            ['name' => 'SimDesigner'],
            ['name' => 'MSC Simufact Welding'],
        ];

        Software::insert($softwares);
    }
}
