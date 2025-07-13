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
            'email' => 'amigo.marineth@dnsc.edu.ph',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Vins Mark vnsmrk',
            'email' => 'vnsmrkdlwn@gmail.com',
            'role' => 'student',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Teacher User',
            'email' => 'caliao.mickyjean@dnsc.edu.ph',
            'role' => 'teacher',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'ICT User',
            'email' => 'mayola.giamae@dnsc.edu.ph',
            'role' => 'ict',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Year Level Head',
            'email' => 'otaza.princessalesanra@dnsc.edu.ph',
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
            'name' => 'vins mark 0214',
            'email' => 'vinsmark.adlawan0214@gmail.com',
            'role' => 'student',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Dimple Jess',
            'email' => 'dmpljssdlwn@gmail.com',
            'role' => 'student',
            'password' => bcrypt('password'),
        ]);
        User::factory()->create([
            'name' => 'Vinstoi kun',
            'email' => 'vintoikun@gmail.com',
            'role' => 'student',
            'password' => bcrypt('password'),
        ]);
    }
}
