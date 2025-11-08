<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Solution;
use Illuminate\Support\Facades\DB;

class SolutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks before truncating
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Solution::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $solutions = [
            ['name' => 'Aerospace Structural Analysis'],
            ['name' => 'Structural Analysis'],
            ['name' => 'Automotive Crash Analysis'],
            ['name' => 'Thermal Analysis'],
            ['name' => 'System Dynamics'],
            ['name' => 'Fatigue & Durability'],
            ['name' => 'Acoustics'],
            ['name' => 'Composite Material Analysis'],
            ['name' => 'Manufacturing Simulation'],
            ['name' => 'Welding Simulation'],
            ['name' => 'Fluid'],
            ['name' => 'VM&C'],
            ['name' => 'Autonomuos'],
            ['name' => 'ICME (Materials)'],
        ];

        // Insert all solutions at once
        Solution::insert($solutions);
    }
}
