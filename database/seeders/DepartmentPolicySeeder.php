<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DepartmentPolicy;

class DepartmentPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DepartmentPolicy::create(['department_id' => 1, 'policy_id' => 2]);
        DepartmentPolicy::create(['department_id' => 1, 'policy_id' => 3]);
        DepartmentPolicy::create(['department_id' => 2, 'policy_id' => 1]);
        DepartmentPolicy::create(['department_id' => 2, 'policy_id' => 3]);
        DepartmentPolicy::create(['department_id' => 1, 'policy_id' => 1]);
    }
}
