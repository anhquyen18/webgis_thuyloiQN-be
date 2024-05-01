<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organization::create(['name' => 'Trung tâm nghiên cứu tài nguyên nước']);
        Organization::create(['name' => 'Phòng quản lí đô thị']);
    }
}
