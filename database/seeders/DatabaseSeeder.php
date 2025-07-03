<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@gmail.com',
            'role' => 'student',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Teacher User',
            'email' => 'teacher@gmail.com',
            'role' => 'teacher',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'ICT User',
            'email' => 'ict@gmail.com',
            'role' => 'ict',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Year Level Head',
            'email' => 'head@gmail.com',
            'role' => 'head',
            'password' => bcrypt('password'),
        ]);
         User::factory()->create([
            'name' => 'Principal User',
            'email' => 'principal@gmail.com',
            'role' => 'principal',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Teacher 2',
            'email' => 'teacher2@gmail.com',
            'role' => 'teacher',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Teacher 3',
            'email' => 'teacher3@gmail.com',
            'role' => 'teacher',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Teacher 4',
            'email' => 'teacher4@gmail.com',
            'role' => 'teacher',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Teacher 5',
            'email' => 'teacher5@gmail.com',
            'role' => 'teacher',
            'password' => bcrypt('password'),
        ]);

         User::factory()->create([
            'name' => 'Student 2',
            'email' => 'student2@gmail.com',
            'role' => 'student',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Student 3',
            'email' => 'student3@gmail.com',
            'role' => 'student',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Student 4',
            'email' => 'student4@gmail.com',
            'role' => 'student',
            'password' => bcrypt('password'),
        ]);
    }
}
