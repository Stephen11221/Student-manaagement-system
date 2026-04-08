<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'Student',
            'slug' => 'student',
            'description' => 'Student role - can view classes, join sessions, submit homework',
        ]);

        Role::create([
            'name' => 'Trainer',
            'slug' => 'trainer',
            'description' => 'Trainer role - can manage classes, create timetables, give homework, mark submissions',
        ]);

        Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Admin role - full system access',
        ]);

        Role::create([
            'name' => 'Department Admin',
            'slug' => 'department_admin',
            'description' => 'Department Admin - can control all users in department',
        ]);

        Role::create([
            'name' => 'Career Coach',
            'slug' => 'career_coach',
            'description' => 'Career Coach - can guide students on career paths',
        ]);
    }
}
