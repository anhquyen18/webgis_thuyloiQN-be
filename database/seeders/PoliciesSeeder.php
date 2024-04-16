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
        Policy::create(['name' => 'Đọc bản vẽ']);
        Policy::create(['name' => 'Xoá bản vẽ']);
        Policy::create(['name' => 'Chỉnh sửa bản vẽ']);
        Policy::create(['name' => 'Làm gì cũng được']);
    }
}
