<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\CRM\database\seeders\LeedsFieldSeeder;
use Modules\CRM\database\seeders\ModuleSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Sales Director',
        //     'email' => 'sales_director@mail.com',
        //     'role' => 'sales-director',
        // ]);
        // User::factory()->create([
        //     'name' => 'Sales Director',
        //     'email' => 'sales_director@mail.com',
        //     'role' => 'sales-director'
        // ]);
        // $this->call(ModuleSeeder::class);
        // $this->call(LeedsFieldSeeder::class);

        // $this->call(SoftwareSeeder::class);
        // $this->call(SolutionSeeder::class);
        // $this->call(IndustrySeeder::class);
        //    $this->call(SoftwareSkillSeeder::class);
        $this->call([
            // TrainingSeeder::class,
            UserSoftwareSkillSeeder::class,
            DepartmentSeeder::class,
            // TrainingSessionSeeder::class,
            // UserSeeder::class,
            // TrainingEnrollmentSeeder::class,
            // CustomerSeeder::class
        ]);
    }
}
