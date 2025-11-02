<?php

namespace Database\Seeders;

use App\Models\SoftwareSkill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SoftwareSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SoftwareSkill::truncate();

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
