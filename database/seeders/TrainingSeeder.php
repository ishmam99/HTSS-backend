<?php

namespace Database\Seeders;

use App\Models\Training;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Training::truncate(); // Optional: clears existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $trainings = [
            [
                'title' => 'Laravel Masterclass',
                'description' => 'Comprehensive Laravel training for backend developers.',
                'start_date' => now(),
                'end_date' => now()->addDays(7),
            ],
            [
                'title' => 'React Frontend Bootcamp',
                'description' => 'Hands-on ReactJS workshop for modern frontend development.',
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(15),
            ],
            [
                'title' => 'Database Design Fundamentals',
                'description' => 'Learn how to design efficient relational databases.',
                'start_date' => now()->addDays(20),
                'end_date' => now()->addDays(25),
            ],
        ];

        foreach ($trainings as $training) {
            Training::create($training);
        }
    }
}
