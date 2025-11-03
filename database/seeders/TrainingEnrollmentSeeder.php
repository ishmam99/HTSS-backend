<?php

namespace Database\Seeders;

use App\Models\Training;
use App\Models\TrainingEnrollment;
use Illuminate\Database\Seeder;

class TrainingEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TrainingEnrollment::truncate();

        $trainings = Training::all();

        foreach ($trainings as $training) {
            TrainingEnrollment::create([
                'training_id' => $training->id,
                'enrolled_on' => now()->subDays(rand(1, 10)),
            ]);
        }
    }
}
