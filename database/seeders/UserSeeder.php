<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
       
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Dashboard-specific users
        $dashboardUsers = [
            ['name' => 'Sales Director', 'email' => 'sales_director@mail.com', 'role' => 'sales-director'],
            ['name' => 'Sales Manager', 'email' => 'sales_manager@mail.com', 'role' => 'sales-manager'],
            ['name' => 'CRM Manager', 'email' => 'crm_manager@mail.com', 'role' => 'crm-manager'],
            ['name' => 'CRM Director', 'email' => 'crm_director@mail.com', 'role' => 'crm-director'],
            ['name' => 'Software Manager', 'email' => 'software_manager@mail.com', 'role' => 'software-manager'],
            ['name' => 'Software Director', 'email' => 'software_director@mail.com', 'role' => 'software-director'],
            ['name' => 'User Manager', 'email' => 'user_manager@mail.com', 'role' => 'user-manager'],
            ['name' => 'User Director', 'email' => 'user_director@mail.com', 'role' => 'user-director'],
            ['name' => 'Customer Manager', 'email' => 'customer_manager@mail.com', 'role' => 'customer-manager'],
            ['name' => 'Customer Director', 'email' => 'customer_director@mail.com', 'role' => 'customer-director'],
            ['name' => 'Partner Manager', 'email' => 'partner_manager@mail.com', 'role' => 'partner-manager'],
            ['name' => 'Partner Director', 'email' => 'partner_director@mail.com', 'role' => 'partner-director'],
            ['name' => 'Training Manager', 'email' => 'training_manager@mail.com', 'role' => 'training-manager'],
            ['name' => 'Training Director', 'email' => 'training_director@mail.com', 'role' => 'training-director'],
            ['name' => 'Website Manager', 'email' => 'website_manager@mail.com', 'role' => 'website-manager'],
            ['name' => 'Website Director', 'email' => 'website_director@mail.com', 'role' => 'website-director'],
        ];

        foreach ($dashboardUsers as $user) {
            User::factory()->create(array_merge($user, [
                'password' => Hash::make('12345678')
            ]));
        }

        // Generic users
        for ($i = 1; $i <= 5; $i++) {
            User::factory()->create([
                'name' => "User $i",
                'email' => "user$i@mail.com",
                'role' => 'user',
                'password' => Hash::make('password123'),
            ]);
        }


    }
}
