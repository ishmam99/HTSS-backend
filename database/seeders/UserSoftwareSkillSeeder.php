<?php

namespace Database\Seeders;

use App\Models\EndUser;
use App\Models\Software;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSoftwareSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
{
    for ($i = 1; $i <= 100; $i++) {

        $num = str_pad($i, 4, '0', STR_PAD_LEFT); // 0001, 0002, ...

        $user = User::create([
            'name' => 'User_Bell_' . $num,
            'email' => 'user_bell_' . $num . '@bell.com',
            'role' => 'end-user',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now()
        ]);

        EndUser::create([
            'customer_id' => 211,
            'user_id' => $user->id,
            'industry_id' => 1,
            'username' => $user->name
        ]);
    }
    $softwares =Software::all();
    foreach($softwares as $software)
    {
        for($i=1;$i<=20;$i++)
       {
          $num = str_pad($i, 3, '0', STR_PAD_LEFT);
        $software->softwareSkill()->create([
        'name' => $software->name .'-'.$num
       ]);
    }
    }
}

}
