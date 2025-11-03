<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Industry;
use Illuminate\Support\Facades\DB;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks before truncating
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Industry::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $industries = [
            ['name' => 'Aerospace'],
            ['name' => 'Automotive'],
            ['name' => 'Railway'],
            ['name' => 'Shipbuilding'],
            ['name' => 'Energy & Power'],
            ['name' => 'Construction & Civil'],
            ['name' => 'Electronics & Semiconductors'],
            ['name' => 'Manufacturing'],
            ['name' => 'Defense & Military'],
            ['name' => 'Medical Devices'],
        ];

        // Insert all rows at once
        Industry::insert($industries);
    }
}
