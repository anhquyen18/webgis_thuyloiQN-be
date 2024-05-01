<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Policy;
use Illuminate\Support\Facades\DB;

class PoliciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Policy::create(['name' => 'Quản lí tổ chức', 'description' => '']);
        Policy::create(['name' => 'Toàn quyền quản lí các tổ chức', 'description' => '']);
        Policy::create(['name' => 'Truy cập thông tin đập', 'description' => '']);
        Policy::create(['name' => 'Toàn quyền quản lí thông tin đập', 'description' => '']);
        Policy::create(['name' => 'Truy cập kế hoạch vận hành', 'description' => '']);
        Policy::create(['name' => 'Toàn quyền quản lí kế hoạch vận hành', 'description' => '']);
        Policy::create(['name' => 'Truy cập kế hoạch bảo trì', 'description' => '']);
        Policy::create(['name' => 'Toàn quyền quản lí kế hoạch bảo trì', 'description' => '']);
        Policy::create(['name' => 'Truy cập kế hoạch an toàn', 'description' => '']);
        Policy::create(['name' => 'Toàn quyền quản lí kế hoạch an toàn', 'description' => '']);
    }
}
