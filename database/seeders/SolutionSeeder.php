<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Solution;

class SolutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Solution::truncate();

        $solutions = [
            [
                'user_id' => 1,
                'name' => 'Aerospace Structural Analysis',
                'domain' => 'Aerospace',
                'description' => 'Solution for composite material simulation and fatigue analysis.'
            ],
            [
                'user_id' => 1,
                'name' => 'Architectural Design Suite',
                'domain' => 'Construction',
                'description' => 'Integrated 3D BIM workflow for large structures.'
            ],
            [
                'user_id' => 1,
                'name' => 'Mechanical Simulation Package',
                'domain' => 'Engineering',
                'description' => 'Complete system for FEA, CFD, and CAD integration.'
            ],
        ];

        foreach ($solutions as $solution) {
            Solution::create($solution);
        }
    }
}
