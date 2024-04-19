<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("object_documents_type")->insert([
            ["name" => "Ảnh"],
            ["name" => "An toàn"],
            ["name" => "Bảo dưỡng"],
            ["name" => "Kế hoạch vận hành"],
        ]);
    }
}
