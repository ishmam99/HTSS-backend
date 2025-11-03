<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TrainingSession;
use App\Models\Training;

class TrainingSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TrainingSession::truncate();

        $trainings = Training::all();

        foreach ($trainings as $training) {
            TrainingSession::create([
                'training_id' => $training->id,
                'session_title' => 'Introduction to ' . $training->title,
                'session_date' => now()->addDays(rand(1, 5)),
                'location' => 'Online Zoom Room',
            ]);

            TrainingSession::create([
                'training_id' => $training->id,
                'session_title' => 'Advanced Topics in ' . $training->title,
                'session_date' => now()->addDays(rand(6, 10)),
                'location' => 'Main Hall - Room A',
            ]);
        }
    }
}
