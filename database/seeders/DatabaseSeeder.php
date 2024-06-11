<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\DepartmentPolicy;
use App\Models\Reservoir;
use Illuminate\Database\Seeder;
use League\CommonMark\Node\Block\Document;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserStatusSeeder::class);
        $this->call(OrganizationsSeeder::class);
        $this->call(DepartmentsSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(PoliciesSeeder::class);
        $this->call(DepartmentPolicySeeder::class);
        $this->call(ReservoirSeeder::class);
        $this->call(ReservoirSafetySeeder::class);
    }
}
