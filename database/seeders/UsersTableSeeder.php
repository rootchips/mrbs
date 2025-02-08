<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $first = User::create([
            'name' => 'Roslan Saidi',
            'uuid' => \Str::uuid(),
            'email' => 'roslan@company.com',
            'password' => bcrypt(1234),
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        $first->assignRole('Administrator');

        $second = User::create([
            'name' => 'Mohammad Hafizzuddin',
            'uuid' => \Str::uuid(),
            'email' => 'hafizzuddin@company.com',
            'password' => bcrypt(1234),
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        $second->assignRole('Administrator');


        $third = User::create([
            'name' => 'Fatin Nur Syafiqah',
            'uuid' => \Str::uuid(),
            'email' => 'fatin@company.com',
            'password' => bcrypt(1234),
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        $third->assignRole('Administrator');

        $fourth = User::create([
            'name' => 'Shamshuhaimi',
            'uuid' => \Str::uuid(),
            'email' => 'sham@company.com',
            'password' => bcrypt(1234),
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        $fourth->assignRole('Administrator');

        $fifth = User::create([
            'name' => 'Roslan Saidi',
            'uuid' => \Str::uuid(),
            'email' => 'mohamadroslansaidi@gmail.com',
            'password' => bcrypt(1234),
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        $fifth->assignRole('Staff');
    }
}
