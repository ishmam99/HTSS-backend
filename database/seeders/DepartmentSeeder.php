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
                'name'        => 'Operations',
                'description' => 'Oversees daily business operations and process management.',
                'status'      => 1,
            ],
            [
                'name'        => 'Finance',
                'description' => 'Manages company finances, budgeting, and accounting.',
                'status'      => 1,
            ],
            [
                'name'        => 'Information Technology',
                'description' => 'Responsible for system infrastructure, security, and software.',
                'status'      => 1,
            ],
            [
                'name'        => 'Sales & Marketing',
                'description' => 'Handles sales strategy, marketing campaigns, and customer growth.',
                'status'      => 1,
            ],
            [
                'name'        => 'Legal',
                'description' => 'Manages legal compliance, contracts, and corporate governance.',
                'status'      => 1,
            ],
            [
                'name'        => 'Administration',
                'description' => 'Handles administrative tasks and internal coordination.',
                'status'      => 1,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
