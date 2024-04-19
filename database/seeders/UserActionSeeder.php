<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("user_action_type")->insert([
            ["name" => "Tạo mới"],
            ["name" => "Xoá"],
            ["name" => "Chỉnh sửa"],
        ]);
    }
}
