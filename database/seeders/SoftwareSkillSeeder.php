<?php

namespace Database\Seeders;

use App\Models\Software;
use App\Models\SoftwareSkill;
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
        SoftwareSkill::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Map skills to relevant software_id, but skill names describe the capability
        $skills = [
            ['software_id' => 1, 'name' => 'Structural Analysis', 'description' => 'Finite Element Analysis for aerospace, automotive, and general structures.'],
            ['software_id' => 2, 'name' => 'Pre/Post Processing', 'description' => 'Meshing, model setup, and results visualization for Nastran.'],
            ['software_id' => 3, 'name' => 'Nonlinear Analysis', 'description' => 'Advanced nonlinear finite element simulations.'],
            ['software_id' => 4, 'name' => 'Fatigue & Durability', 'description' => 'Predict component life under cyclic loading.'],
            ['software_id' => 5, 'name' => 'Multibody Dynamics', 'description' => 'Simulate mechanisms and motion of assemblies.'],
            ['software_id' => 6, 'name' => 'Metal Forming Simulation', 'description' => 'Simulate forging, stamping, and forming processes.'],
            ['software_id' => 7, 'name' => 'Composite Material Modeling', 'description' => 'Analyze heterogeneous materials and composite structures.'],
            ['software_id' => 8, 'name' => 'CAE Platform Modeling', 'description' => 'Integrated modeling, simulation, and meshing environment.'],
            ['software_id' => 9, 'name' => 'Multidisciplinary Simulation', 'description' => 'Perform coupled simulations across different physics domains.'],
            ['software_id' => 10, 'name' => 'Acoustics & Vibration', 'description' => 'Noise and vibration analysis for components and systems.'],
        ];

        SoftwareSkill::insert($skills);
    }
}
