<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Software;

class SoftwareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Software::truncate();

        $software = [
            ['name' => 'AutoCAD 2025', 'vendor' => 'Autodesk', 'version' => '2025', 'release_date' => '2025-01-01', 'software_skill_id' => 1 , 'user_id' => 1],
            ['name' => 'SolidWorks 2024', 'vendor' => 'Dassault Systemes', 'version' => '2024', 'release_date' => '2024-05-01', 'software_skill_id' => 2 , 'user_id' => 1 ],
            ['name' => 'ANSYS Fluent', 'vendor' => 'ANSYS Inc.', 'version' => '2024 R1', 'release_date' => '2024-03-01', 'software_skill_id' => 3 , 'user_id' => 1],
            ['name' => 'MATLAB R2024a', 'vendor' => 'MathWorks', 'version' => 'R2024a', 'release_date' => '2024-04-15', 'software_skill_id' => 4 , 'user_id' => 1],
        ];

        foreach ($software as $s) {
            Software::create($s);
        }
    }
}
