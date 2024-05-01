<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObjectActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("object_activity_type")->insert([
            ["name" => "Kế hoạch vận hành", 'table_name' => 'reservoir_operation'],
            ["name" => "Kế hoạch bảo dưỡng", 'table_name' => 'reservoir_,maintainance'],
            ["name" => "Kế hoạch kiểm tra an toàn", 'table_name' => 'reservoir_safety'],
        ]);
    }
}
