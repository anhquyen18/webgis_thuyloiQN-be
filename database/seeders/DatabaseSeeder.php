<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\DepartmentPolicy;
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
        $this->call(DepartmentsSeeder::class);
        $this->call(PoliciesSeeder::class);
        $this->call(DepartmentPolicySeeder::class);
        $this->call(OrganizationsSeeder::class);
        $this->call(UserActionSeeder::class);
        $this->call(ObjectActivityTypeSeeder::class);
        $this->call(DocumentTypeSeeder::class);
    }
}
