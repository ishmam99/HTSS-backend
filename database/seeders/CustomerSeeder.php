<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\EndUser;
use App\Models\EndUserSoftware;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

public function run(): void
{
     DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Customer::truncate();
        Industry::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    $industries = [
        'Aerospace & Defense',
        'Agriculture, Forestry & Fishing',
        'Architecture, Engineering & Construction',
        'Automotive, Transportation & Mobility',
        'Consumer Goods & Packaging',
        'Education Sector',
        'Energy, Process & Utilities',
        'Entertainment, Leisure and Hospitality',
        'Financial & Business Services',
        'Floating Structures',
        'General, Merchandise & Grocery',
        'Healthcare Industry',
        'Heavy Machinery Equipment',
        'Hi-tech',
        'Information Technology',
        'Life Science & Medical Device',
        'Local Government Sector',
        'Manufacturing',
        'Security Industry',
        'R&D Services',
        'Safety Systems',
    ];

    foreach ($industries as $industryIndex => $industryName) {
        $industry = Industry::create([
            'name' => $industryName,
        ]);

        // Extract short code (e.g., "AD" from "Aerospace & Defense")
       $shortCode = collect(explode(' ', $industryName))
            ->reject(fn($word) => $word === '&') // skip "&"
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->join('');

        for ($i = 1; $i <= 10; $i++) {
            $formattedNumber = str_pad($i, 3, '0', STR_PAD_LEFT); // 001, 002, 003...
            $customerCode = "Customer_{$shortCode}_{$formattedNumber}";

            $user = User::factory()->create([
                'name' => $customerCode,
                'email' => strtolower($customerCode) . '@mail.com',
                'role' => 'customer',
                'password' => Hash::make('password123'),
            ]);

          $customer =  Customer::create([
                'user_id' => $user->id,
                'industry_id' => $industry->id,
                'phone' => '01700000' . $i,
                'address' => "Address $i",
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'postal_code' => '1200' . $i,
                'status' => 1,
            ]);
            for($j = 1 ; $j <= 3; $j++)
            {
                 $formattedNumber1 = str_pad($j, 3, '0', STR_PAD_LEFT); // 001, 002, 003...
              $userCode =  "User_C{$i}_{$shortCode}_{$formattedNumber1}";
            $user = User::factory()->create([
                'name' => $userCode,
                'email' => strtolower($userCode) . '@mail.com',
                'role' => 'end-user',
                'password' => Hash::make('password123'),
            ]);
            $endUser = EndUser::create([
                'user_id' => $user->id,
                'industry_id' => $industry->id,
                'customer_id' => $customer->id,
            ]);
            EndUserSoftware::create([
                'end_user_id' => $endUser->id,
                'software_id' => $j,
                'level' => $j == 1? 'Basic' : ($j == 2 ? 'Advance': 'Intermidiate')
            ]);

            }

        }
    }
}
}
