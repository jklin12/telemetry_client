<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@telemetry.client',
                'password' => Hash::make('123456'),
                'isAdmin' => true
            ],
            [
                'name' => 'User',
                'email' => 'user@telemetry.client',
                'password' => Hash::make('123456'),
                'isAdmin' => false
            ]
        ]);
    }
}
