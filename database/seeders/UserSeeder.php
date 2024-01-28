<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        User::create([
            'username' => 'admin',
            'name' => 'Admin',
            'password' => Hash::make('admin2000'),
            'email' => 'admin@tamky.click',
            'department_id' => '2',
            'status_id' => '1',
        ]);

        User::create([
            'username' => 'testuser01',
            'name' => 'User01',
            'password' => Hash::make('anhquyen1809'),
            'email' => 'user01@tamky.click',
            'department_id' => '1',
            'status_id' => '1',
        ]);
    }
}
