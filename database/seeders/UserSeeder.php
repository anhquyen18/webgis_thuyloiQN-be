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

        // User::create([
        //     'username' => 'admin1',
        //     'name' => 'Admin',
        //     'email' => 'anhquyen.dut@gmail.com',
        //     'password' => Hash::make('admin1122'),
        //     'avatar' => 'admin_avatar.jpg',
        //     'gender' => 'Nam',
        //     'birthday' => Carbon::createFromFormat('Y-m-d', '2000-09-18'),
        //     'phone_number' => '0355808535',
        //     'status_id' => 1,
        //     'department_id' => 2,
        //     'organization_id' => 1,
        // ]);

        // User::create([
        //     'username' => 'admin2',
        //     'name' => 'Admin',
        //     'password' => Hash::make('admin1122'),
        //     'email' => 'anhquyen.dut@gmail.com',
        //     'status_id' => '2',
        //     'department_id' => '2',
        //     'organization_id' => '1',
        // ]);

        // User::create([
        //     'username' => 'admin3',
        //     'name' => 'Admin',
        //     'password' => Hash::make('admin1122'),
        //     'email' => 'anhquyen.dut@gmail.com',
        //     'status_id' => '1',
        //     'department_id' => '2',
        //     'organization_id' => '2',
        // ]);

        // User::create([
        //     'username' => 'admin4',
        //     'name' => 'Admin',
        //     'password' => Hash::make('admin1122'),
        //     'email' => 'anhquyen.dut@gmail.com',
        //     'status_id' => '3',
        //     'department_id' => '1',
        //     'organization_id' => '2',
        // ]);

        // User::create([
        //     'username' => 'testuser1',
        //     'name' => 'Test User',
        //     'password' => Hash::make('testuser1122'),
        //     'email' => 'anhquyen.dut@gmail.com',
        //     'status_id' => '1',
        // ]);

        for ($i = 0; $i < 100; $i++) {
            User::create([
                'username' => Str::random(8), // Generate a random username
                'name' => Str::random(10), // Generate a random name
                'email' => Str::random(10) . '@example.com', // Generate a random email
                'password' => Hash::make('anhquyen11'), // Generate a random password
                'birthday' => now()->subYears(random_int(18, 30)), // Generate a random birthday for users between 18 and 30 years old
                'status_id' => random_int(1, 3), // Assuming you have 3 status options
                'department_id' => random_int(1, 2),
                'organization_id' => random_int(1, 2)
            ]);
        }

        // for ($i = 0; $i < 1000; $i++) {
        //     User::create([
        //         'username' => Str::random(8), // Generate a random username
        //         'name' => Str::random(10), // Generate a random name
        //         'email' => Str::random(10) . '@example.com', // Generate a random email
        //         'password' => Hash::make('anhquyen11'), // Generate a random password
        //         'birthday' => now()->subYears(random_int(18, 30)), // Generate a random birthday for users between 18 and 30 years old
        //         'status_id' => random_int(1, 3), // Assuming you have 3 status options
        //     ]);
        // }



    }
}
