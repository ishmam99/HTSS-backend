<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name'        => 'Human Resources',
                'description' => 'Handles recruitment, employee relations, and HR policies.',
                'status'      => 1,
            ],
            [
                'name'        => 'Finance',
                'description' => 'Manages company finances, budgeting, and accounting.',
                'status'      => 1,
            ],
            [
                'name'        => 'IT',
                'description' => 'Responsible for system infrastructure and software.',
                'status'      => 0,
            ],
            [
                'name'        => 'Marketing',
                'description' => 'Handles marketing campaigns and brand promotion.',
                'status'      => 1,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
