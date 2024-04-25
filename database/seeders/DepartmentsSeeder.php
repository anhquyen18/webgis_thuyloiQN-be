<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::create(['name' => 'Phòng kỹ thuật']);
        Department::create(['name' => 'Phòng thư ký 23']);
        Department::create(['name' => 'Phòng thư ký biên tập', 'organization_id' => 2]);
        Department::create(['name' => 'ADMIN']);
    }
}
