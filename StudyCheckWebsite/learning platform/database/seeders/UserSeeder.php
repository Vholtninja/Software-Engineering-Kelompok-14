<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // Teacher
        User::create([
            'name' => 'John Teacher',
            'email' => 'teacher@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'bio' => 'Experienced mathematics teacher with 10+ years of teaching experience.',
            'institution' => 'Tech University',
            'is_verified' => true,
            'points' => 1500,
            'email_verified_at' => now(),
        ]);

        // Expert
        User::create([
            'name' => 'Jane Expert',
            'email' => 'expert@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'expert',
            'bio' => 'Software engineering expert specializing in web development.',
            'institution' => 'Tech Corp',
            'level' => 'advanced',
            'is_verified' => true,
            'points' => 2500,
            'email_verified_at' => now(),
        ]);

        // Student
        User::create([
            'name' => 'Alice Student',
            'email' => 'student@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'bio' => 'Computer Science student passionate about learning new technologies.',
            'institution' => 'State University',
            'level' => 'intermediate',
            'points' => 750,
            'email_verified_at' => now(),
        ]);

        // Create more sample users
        User::factory(20)->create();
    }
}