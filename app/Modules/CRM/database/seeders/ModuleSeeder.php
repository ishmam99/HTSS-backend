<?php

namespace Modules\CRM\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Module::create([
            'name' => 'Leads',
            'label' => 'Leads',
            'icon' => 'fa-solid fa-people-roof'
        ]);
        Module::create([
            'name' => 'Accounts',
            'label' => 'Accounts',
            'icon' => 'fa-regular fa-address-card'
        ]);
        Module::create([
            'name' => 'Contacts',
            'label' => 'Contacts',
            'icon' => 'fa-regular fa-address-book'
        ]);
        Module::create([
            'name' => 'Contracts',
            'label' => 'Contracts',
            'icon' => 'fa-solid fa-file-contract'
        ]);
        Module::create([
            'name' => 'Deals',
            'label' => 'Deals',
            'icon' => 'fa-solid fa-handshake'
        ]);
        Module::create([
            'name' => 'Invoices',
            'label' => 'Invoices',
            'icon' => 'fa-solid fa-receipt'
        ]);
        Module::create([
            'name' => 'Projects',
            'label' => 'Projects',
            'icon' => 'fa-solid fa-diagram-project'
        ]);
    }
}
