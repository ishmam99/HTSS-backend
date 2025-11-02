<?php

namespace Database\Seeders;

use App\Models\SoftwareSkill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoftwareSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\SoftwareSkill::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $skills = [
            ['name' => 'AutoCAD', 'description' => '2D/3D drafting tool.'],
            ['name' => 'SolidWorks',  'description' => '3D CAD modeling software.'],
            ['name' => 'ANSYS',  'description' => 'Finite Element Analysis and CFD.'],
            ['name' => 'MATLAB', 'description' => 'Data analysis and simulation environment.'],
            ['name' => 'Revit',  'description' => 'BIM modeling and architectural design.'],
        ];

        foreach ($skills as $skill) {
            SoftwareSkill::create($skill);
        }
    }
}
