<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new \App\Models\User();
        $user->name = 'Admin';
        $user->email = 'admin@email.com';
        $user->email_verified_at = now();
        $user->password = '12345678';
        $user->role = '2'; //admin
        $user->verified = true;
        $user->save();
    }
}
