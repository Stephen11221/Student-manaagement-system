<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $accounts = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'department' => 'Administration',
            ],
            [
                'name' => 'Trainer User',
                'email' => 'trainer@example.com',
                'role' => 'trainer',
                'department' => 'Academics',
            ],
            [
                'name' => 'Career Coach',
                'email' => 'coach@example.com',
                'role' => 'career_coach',
                'department' => 'Student Success',
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'student',
                'department' => 'General Studies',
            ],
        ];

        foreach ($accounts as $account) {
            User::query()->firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'department' => $account['department'],
                    'password' => 'password',
                ]
            );
        }

        $student = User::where('email', 'test@example.com')->first();
        $coach = User::where('email', 'coach@example.com')->first();

        if ($student && $coach && ! $student->career_coach_id) {
            $student->update(['career_coach_id' => $coach->id]);
        }
    }
}
