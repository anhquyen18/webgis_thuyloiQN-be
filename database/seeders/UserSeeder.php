<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // echo Carbon::now();

        User::create([
            'username' => 'admin1',
            'name' => 'Admin1',
            'email' => 'anhquyen.dut@gmail.com',
            'password' => Hash::make('admin1122'),
            'gender' => 'Nam',
            'birthday' => Carbon::createFromFormat('Y-m-d', '2000-09-18'),
            'phone_number' => '0355808535',
            'status_id' => 1,
            'department_id' => 2,
        ]);

        User::create([
            'username' => 'admin2',
            'name' => 'Admin2',
            'password' => Hash::make('admin1122'),
            'email' => 'anhquyen.dut@gmail.com',
            'status_id' => '2',
            'department_id' => '2',
        ]);

        User::create([
            'username' => 'admin3',
            'name' => 'Admin3',
            'password' => Hash::make('admin1122'),
            'email' => 'anhquyen.dut@gmail.com',
            'status_id' => '1',
            'department_id' => '2',
        ]);

        User::create([
            'username' => 'admin4',
            'name' => 'Admin4',
            'password' => Hash::make('admin1122'),
            'email' => 'anhquyen.dut@gmail.com',
            'status_id' => '3',
            'department_id' => '1',
        ]);

        User::create([
            'username' => 'testuser1',
            'name' => 'Test User',
            'password' => Hash::make('testuser1122'),
            'email' => 'anhquyen.dut@gmail.com',
            'status_id' => '1',
        ]);

        User::factory()->times(2000)->create();
    }
}
